<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use App\Utils\ContactUtil;
use App\Utils\ModuleUtil;
use App\Utils\NotificationUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;

use App\Business;
use App\BusinessLocation;
use App\Contact;

use Yajra\DataTables\Facades\DataTables;
use App\Events\ContactCreatedOrModified;

class CreateBusinessOwnerContact extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:businessOwnerContact {--business_id=} {--affiliate_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Création automatique des representation des business';


    protected $commonUtil;
    protected $contactUtil;
    protected $moduleUtil;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil, ContactUtil $contactUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->contactUtil = $contactUtil;
        $this->moduleUtil = $moduleUtil;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //dd("Business_id=". $this->option('business_id')." ||| affiliate_id=". $this->option('affiliate_id'));
        try {
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', '512M');

            $business_id = env('ADMINISTRATOR_BUSINESS');
            $businessownercontact_field = env('ADMINISTRATOR_CONTACT_CUSTOMLABEL');

            $business = Business::find($business_id);
            $prefixes = $business->ref_no_prefixes;






            //si il n'y a pas de business passé en argument
            $nocontact_businesses = [];
            if (empty($this->option('business_id'))) {
                $businessownercontact_ids = (Contact::where('business_id', $business_id)
                    ->whereNotNull($businessownercontact_field)
                    ->pluck($businessownercontact_field)->toArray());
                //($businessownercontact_ids);

                $nocontact_businesses = Business::whereNotIn('id', $businessownercontact_ids)->get();

            } else {
                $nocontact_business = Business::find($this->option('business_id'));
                if ($nocontact_business) {
                    $nocontact_businesses[] = $nocontact_business;
                }
            }
            //dd(count($nocontact_businesses));




            //je recupere le owner et je crée le contact à partir de ces informations
            DB::beginTransaction();
            foreach ($nocontact_businesses as $key => $nocontact_business) {
                try {
                    $nocontact_location = BusinessLocation::where('business_locations.business_id', $nocontact_business->id)->first();
                    //dd($nocontact_business->name);
                    $input = [];
                    $name_array = [];
                    $name_array[] = $nocontact_business->owner->surname;
                    $name_array[] = $nocontact_business->owner->first_name;
                    $name_array[] = $nocontact_business->owner->last_name;
                    $input['name'] = trim(implode(' ', $name_array));

                    //$input['is_export'] = true;
                    $input[env('ADMINISTRATOR_CONTACT_CUSTOMLABEL')] = $nocontact_business->id;
                    $input[env('ADMINISTRATOR_COMMISSION_CUSTOMLABEL')] = $this->option('affiliate_id');
                    //$input['export_custom_field_2'] = $request->input('export_custom_field_2');//affiliate

                    $input['business_id'] = $business_id;
                    $input['created_by'] = env('ADMINISTRATOR_SUBSCRIPTION_USERAGENT');

                    $input['credit_limit'] = null;
                    $input['opening_balance'] = $this->commonUtil->num_uf(0);

                    $input['email'] = $nocontact_business->owner->email;
                    $input['type'] = 'customer';
                    $input['supplier_business_name'] = $nocontact_business->name;
                    $input['mobile'] = $nocontact_location->mobile ?? '#';
                    $input['landline'] = $nocontact_location->landmark;
                    $input['alternate_number'] = $nocontact_location->alternate_number;
                    $input['city'] = $nocontact_location->city;
                    $input['state'] = $nocontact_location->state;
                    $input['country'] = $nocontact_location->country;
                    $input['address_line_1'] = $nocontact_location->landmark;
                    $input['customer_group_id'] = env('H360POS_CUSTOMER_GROUP_ID') ?? null;
                    $input['zip_code'] = $nocontact_location->zip_code;

                    $input['credit_limit'] = $this->commonUtil->num_uf(env('H360POS_CUSTOMER_CREDIT_LIMIT'));
                    $input['pay_term_type'] = env('H360POS_CUSTOMER_PAY_TERM_TYPE');
                    $input['pay_term_number'] = $this->commonUtil->num_uf(env('H360POS_CUSTOMER_PAY_TERM_NUMBER'));

                    $input['customer_group_id'] = env('H360POS_CUSTOMER_GROUP_ID');
                    $input['opening_balance'] = 0;


                    /*          
                              $input=[
                          "id" => 110,
                          "business_id" => 4,
                          "type" => "customer",
                          "supplier_business_name" => null,
                          "name" => "Walk-In Customer AAA",
                          "prefix" => null,
                          "first_name" => null,
                          "middle_name" => null,
                          "last_name" => null,
                          "email" => null,
                          "contact_id" => "CO0001",
                          "contact_status" => "active",
                          "tax_number" => null,
                          "city" => null,
                          "state" => null,
                          "country" => null,
                          "address_line_1" => null,
                          "address_line_2" => null,
                          "zip_code" => null,
                          "dob" => null,
                          "mobile" => "",
                          "landline" => null,
                          "alternate_number" => null,
                          "pay_term_number" => null,
                          "pay_term_type" => null,
                          "credit_limit" => "0.0000",
                          "created_by" => 5,
                          "converted_by" => null,
                          "converted_on" => null,
                          "balance" => "0.0000",
                          "total_rp" => 0,
                          "total_rp_used" => 0,
                          "total_rp_expired" => 0,
                          "is_default" => 1,
                          "shipping_address" => null,
                          "shipping_custom_field_details" => null,
                          "is_export" => 0,
                          "export_custom_field_1" => null,
                          "export_custom_field_2" => null,
                          "export_custom_field_3" => null,
                          "export_custom_field_4" => null,
                          "export_custom_field_5" => null,
                          "export_custom_field_6" => null,
                          "position" => null,
                          "customer_group_id" => null,
                          "crm_source" => null,
                          "crm_life_stage" => null,
                          "custom_field1" => null,
                          "custom_field2" => null,
                          "custom_field3" => null,
                          "custom_field4" => null,
                          "custom_field5" => null,
                          "custom_field6" => null,
                          "custom_field7" => null,
                          "custom_field8" => null,
                          "custom_field9" => null,
                          "custom_field10" => null,
                          "deleted_at" => null,
                          "created_at" => "2023-05-02 16:56:06",
                          "updated_at" => "2023-05-02 16:56:06"
                        ];
                              //dd(Contact::where('business_id', $business_id)->limit(5)->get());
                              $ref_count=Contact::where('business_id', $business_id)->count();
                              $input['contact_id'] =$this->moduleUtil->generateReferenceNumber('contacts', $ref_count);
                              
                              
                              
                              $contact = Contact::create($input);
                              DB::commit();
                              dd($contact);
                  */



                    //$contact = Contact::create($input);
                    $contact = $this->contactUtil->createNewContact($input);
                    //event(new ContactCreatedOrModified($input, 'added'));

                    //$this->moduleUtil->getModuleData('after_contact_saved', ['contact' => $contact['data'], 'input' => $input]);
                    //$this->contactUtil->activityLog($contact['data'], 'added');



                    //dd($contact);

                } catch (\Exception $e) {
                    continue;
                }

            }
            DB::commit();





        } catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());

            exit($e->getMessage());
        }

        return Command::SUCCESS;
    }
}
