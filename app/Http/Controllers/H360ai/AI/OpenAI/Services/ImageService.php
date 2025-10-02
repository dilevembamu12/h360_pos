<?php

/**
 * @package ImageService
 * @author TechVillage <support@techvill.org>
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 * @created 22-03-2023
 */

namespace Modules\OpenAI\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Image as Images;
use Modules\OpenAI\Entities\{
    Image,
    ContentTypeMeta,
    ContentType
};
use App\Models\{
    User,
    Team,
    TeamMemberMeta
};
use App\Traits\ApiResponse;
 class ImageService
 {
    use ApiResponse;

    protected $formData;
    public $imageName;
    public $images;
    protected $promt;
    protected $imageNames;
    protected $class;

    /**
     * Initialize
     *
     * @param string $service
     * @return void
     */
    public function __construct($formData = null, $imageName = null, $images = null, $promt = null, $imageNames = null, $class = null)
    {
        $this->formData = $formData;
        $this->imageName = $imageName;
        $this->images = $images;
        $this->promt = $promt;
        $this->imageNames = $imageNames;
        $this->class = $class;
    }

    /**
     * Define storage path
     * This path will come dynamically from admin settings
     * @return [type]
     */
    public function storagePath()
    {
        return objectStorage()->url($this->uploadPath());
    }


    /**
     * Image create
     * @param mixed $data
     *
     * @return [type]
     */
    public function createImage($data)
    {
        $this->formData = $data;
        $this->formData['promt'] = filteringBadWords($this->formData['promt']);
        return $this->validate();
    }

    /**
     * Check image service provider
     * @return [type]
     */
    public function checkProvider()
    {
        if (filled(request('provider'))) {
            $class = 'Modules\OpenAI\Libraries'. "\\" . providerClassName(request('provider'));
        } else {
            $usedApi = ContentTypeMeta::where(['key' => 'imageCreateFrom'])->value('value');
            $usedApi = $this->filterImageProviders($usedApi);
            $class = 'Modules\OpenAI\Libraries'. "\\" . providerClassName($usedApi[0]);
        }
        return $class;
    }

    /**
     * Go the the specific class
     *
     * @return array
     */
    public function imageClass()
    {
        $class = $this->checkProvider();
 
        if (class_exists($class, true)) {
            $this->class = new $class($this);
            return $this->preparePromt();
        } else {
            return [
                'response' => __('Please Provide a Valid Provider'),
                'status' => 'failed',  
            ];
        }
    }

    /**
     * prepare promt
     * @return [type]
     */
    public function preparePromt()
    {
        return $this->class->promt($this->formData);
    }

     /**
     * validate form data
     * @return array
     */
    public function validate()
    {
        app('Modules\OpenAI\Http\Requests\ImageStoreRequest')->safe();
        return $this->imageClass();
    }

     
    /**
     * Create upload path
     * @return [type]
     */
    public function uploadPath()
	{
		return createDirectory(join(DIRECTORY_SEPARATOR, ['public', 'uploads','aiImages']));
	}

    /**
	 * Upload thumbnail path
     *
     * @param string $size
	 * @return string
	 */
	protected function thumbnailPath($size = 'small')
	{
		return createDirectory(join(DIRECTORY_SEPARATOR, ['public', 'uploads', config('openAI.thumbnail_dir'), $size]));
	}

    /**
     * Store Images
     * @param mixed $data
     *
     * @return [type]
     */
    public function upload($url)
    {
        $filename = preg_replace('/[^A-Za-z0-9\_]/', '', str_replace(" ", "_",  $this->createName(request('promt'))));
        $filename = md5(uniqid()) . "." . "jpg";
        $this->imageName = $filename;
        $image = objectStorage()->put($this->uploadPath() . DIRECTORY_SEPARATOR . $filename, file_get_contents($url));
        $this->makeThumbnail($this->imageName);
        return $image;
    }

  

    /**
     * make thumbnail
     * @param  int  $id
     * @return boolean
     */
    public function makeThumbnail($uploadedFileName)
    {
        $uploadedFilePath = objectStorage()->url($this->uploadPath());
        $thumbnailPath = createDirectory($this->uploadPath());
        $this->resizeImageThumbnail($uploadedFilePath, $uploadedFileName, $thumbnailPath);
        return true;
    }

    /**
     * Resize image thumbnail
     *
     * @param string $uploadedFilePath
     * @param string $uploadedFileName
     * @param string $thumbnailPath
     * @param string $oldFileName
     * @return void
     */
	public function resizeImageThumbnail($uploadedFilePath, $uploadedFileName, $thumbnailPath, $oldFileName = null)
	{
		$sizes = $this->sizeRatio();
        $imagePath = str_replace('\\', '/', $uploadedFilePath. DIRECTORY_SEPARATOR . $uploadedFileName);
		foreach ($sizes as $name => $ratio) {
            try {
                $img = Images::make($imagePath);

                $thumbnailPath = createDirectory($this->thumbnailPath($name));
                foreach ($ratio as $key => $value) {
                    $img->resize($img->height(), $value, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    
                    objectStorage()->put($thumbnailPath . DIRECTORY_SEPARATOR .  $uploadedFileName, $img->stream());
                }
            } catch (\Intervention\Image\Exception\NotReadableException $e) {
                // Show some exception message
            }
		}
	}

     /**
     * Size ratio
     *
     * @return array
     */
	public function sizeRatio()
	{
		return [
            'small' => [150 => 150],
            'medium' => [512 => 512]
        ];
	}

    /**
     * Image store in DB
     * @param mixed $images
     *
     * @return [type]
     */
    public function storeData($image)
    {
        return Image::insertGetId($image);
    }

    /**
     * Team member meta insert or update
     * @param array $imagedata
     *
     * @return bool|array
     */
    public function storeTeamMeta($words)
    {
        $memberData = Team::getMember(auth()->user()->id);
        if (!empty($memberData)) {
            $usage = TeamMemberMeta::getMemberMeta($memberData->id, 'image_used');
            if (!empty($usage)) {
                return $usage && $usage->increment('value', $words); 
            }
        }
        return false;
    }

    /**
     * Image Name Creation
     *
     * @param null $name
     * @return string
     */
    public function createName($name = null)
    {
        return !empty($name) ? substr($name, 0, 100) : Str::random(100);
    }


     /**
      * Slug Creator

      * @param string $name
      * @return string
      */
    public function createSlug($name)
    {
        if (!empty($name)) {

            if (strlen($name) > 120) {
                $name = substr($name, 0, 120);
            }

            $slug = cleanedUrl($name);

            if(Image::whereSlug($slug)->exists()) {
                $slug = $slug . '-' . time();
            }

            return $slug;
        }
    }

    /**
     * Get All images
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAll()
    {
        $result = Image::query();
        $result = $result->where('user_id', auth()->user()->id);

        return $result->orderBy('id', "DESC");
    }

    /**
     * Core Model
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function model()
    {
        return Image::with(['user:id,name', 'user.metas']);
    }

     /**
     * Details of image
     * @return string
     */
    public function details($id)
    {
        $details = $this->model()->where('id', $id)->first();
        return !empty($details) ? $details : false;
    }

    /**
     * Delete image
     *
     * @param mixed $id
     * @return bool
     */
    public function delete($id)
    {
        $image = $this->model()->where('id', $id)->first();
        $isDeleted = empty($image) ? false : $image->delete();
        if ($isDeleted) {
            return $this->unlinkFile($image->original_name);
        }

        return $isDeleted;
    }

    /**
     * Unlink image
     * @param mixed $name
     *
     * @return [type]
     */
    protected function unlinkFile($name)
    {
        if (isExistFile($this->imagePath($name))) {
            objectStorage()->delete($this->imagePath($name));
        }
        
        return true;
    }

    /**
     * Image path
     * @param mixed $name
     *
     * @return [type]
     */
    public static function imagePath($name)
    {
        return 'public' . DIRECTORY_SEPARATOR . 'uploads'. DIRECTORY_SEPARATOR . 'aiImages'. DIRECTORY_SEPARATOR . $name;
    }

    /**
     * image url through id
     * @param mixed $id
     *
     * @return [type]
     */
    public static function imageUrl($id)
    {
        $image = self::model()->where('id', $id)->first();
        return !empty($image) ? self::imagePath($image->original_name) : '';
    }

    /**
     * image view through id
     * @param mixed $id
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function view($id)
    {
        return $this->model()->where('id', $id)->firstOrFail();
    }

    /**
     * Image By Slug
     * @param string $slug
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function imageBySlug($slug)
    {
        return $this->model()->whereSlug($slug)->firstOrFail();
    }

      /**
     * Users Data
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function users()
    {
        return User::get();
    }

    /**
     * Size of image
     *
     * @return string
     */
    public function sizes()
    {
        return config('openAI.size');
    }

    /**
     * get hight width data
     *
     * @param string $string
     * @return array
     */
    public function explodedData($string)
    {
       return explode("x", $string);
    }

    /**
     * Image By Slug
     * @param string $slug
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function bySlug($slug)
    {
        $result = self::model()->where('slug', $slug);
        $result->where('user_id', auth()->user()->id);

        return $result->firstOrFail();
    }

    /**
     * Related Images
     * @param string $name
     * @param string $id
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function relatedImages($name, $id)
    {
        $result = $this->model()->whereLike('name', $name)->where('id', '!=', $id);
        $result->where('user_id', auth()->user()->id);

        return $result->take(4)->get();
    }

    /**
     * Variants
     * @param array data
     *
     * @return \Illuminate\Database\Eloquent\Model|array
     */
    public function variants($data)
    {
        $result = $this->model();
        $result = $result->where('user_id', auth()->user()->id);

        return $result->where('created_at', $data['created_at'])->where('id', '!=', $data['id'])->orderBy('id', 'DESC')->get();
    }

    /**
     * prepare promt
     * 
     * @param array $data
     * @param string $size
     * 
     * @return [type]
     */
    public function prepareImageData($data = [], $favorites = [], $size)
    {
        $imageItems = [];

        foreach ($data as $image) {
            $imageItems[] = [
                'id'=> $image->id,
                'slug' => $image->slug,
                'name'=> $image->name,
                'promt' => $image->promt,
                'originalImageUrl'=> $image->imageUrl(),
                'imageUrl'=> $image->imageUrl(['thumbnill' => true, 'size' => $size]),
                'size' => $image->size,
                'is_favorite'=> in_array($image->id, $favorites) ? true : false,
                'created_at' => timeZoneFormatDate($image->created_at) . ', ' . timeZoneGetTime($image->created_at),
                'art_style' => $image->art_style,
                'lighting_style' => $image->lighting_style,
            ];
        }

        return $imageItems;
    }
    
    /**
     * Image Model
     *
     * @return mixed
     */
    public function getModel()
    {
        return ContentType::getData('image_maker');
    }

    /* Filter Resolation  
     *
     * @param array $data
     * @return [type]
     */
    public function filterResolutions($data)
    {
        $data = array_filter($data, function ($key) {
            return strpos($key, '_resulation') !== false;
        }, ARRAY_FILTER_USE_KEY);

        $mergedData = array_reduce($data, function ($carry, $item) {
            return array_merge($carry, $item);
        }, []);

        return $mergedData;
    }

    /**
     * Process Resolation Data  
     *
     * @return [type]
     */
    public function processResolutionsData()
    {
        $data = $this->getModel();
        $providers = $this->filterImageProviders($data->imageCreateFrom);
        $newData = [];

        foreach ($providers as $provider) {
            $newData[$provider] = processPreferenceData($data->{$provider . '_resulation'});
        }

        $mergedData = array_reduce($newData, function ($carry, $item) {
            return array_merge($carry, $item);
        }, []);

        return $mergedData;
    }

    /**
     * @param string $data
     * 
     * @return array
     */
    public function filterResolution($data)
    {
        $resolution = [];
        $engines = processPreferenceData(preference($data));

        foreach ($engines as $engine) {
            $resolutions[$engine] = config('openAI.size')[$engine];
        }

        $resolution = array_reduce($resolutions, function ($carry, $item) {
            return array_merge($carry, $item);
        }, []);

        return $resolution;
    }

    /**
     * Filter Image Providers
     *
     * @param mixed $data
     * @return [type]
     */
    public function filterImageProviders($data) {
        $imageCreateFrom = processPreferenceData($data);
        $imageCreateFrom = array_filter($imageCreateFrom , function ($value) {
            return $value === '1';
        });
        $providers = array_keys($imageCreateFrom);
        return $providers;
    }
}
