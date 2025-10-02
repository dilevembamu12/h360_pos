<div class="row">
    @forelse($all_videos as $video)
        <div class="col-md-4 col-sm-6">
            <div class="video-card">
                <div class="ratio ratio-16x9">
                    <iframe src="https://www.youtube.com/embed/{{$video->video_id}}?rel=0" title="{{$video->title}}" allowfullscreen></iframe>
                </div>
                <div class="video-card-body">
                    <h5 class="video-card-title">{{$video->title}}</h5>
                    <p class="video-card-text">{{ Str::limit($video->description, 100) }}</p>
                </div>
            </div>
        </div>
        @if($loop->iteration % 3 == 0)
            <div class="clearfix"></div>
        @endif
    @empty
        <div class="col-xs-12">
            <p>Aucune vidéo n'a encore été ajoutée.</p>
        </div>
    @endforelse
</div>