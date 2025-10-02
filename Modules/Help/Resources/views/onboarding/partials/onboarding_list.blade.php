<div class="box">
    <div class="box-header">
        <h3 class="box-title">Gérer les Guides Interactifs</h3>
        <div class="box-tools">
            <button type="button" class="btn btn-block btn-primary btn-modal" 
                data-href="{{action([\Modules\Help\Http\Controllers\OnboardingController::class, 'create'])}}" 
                data-container=".onboarding_step_modal">
                <i class="fa fa-plus"></i> Ajouter un guide</button>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped" id="onboarding_steps_table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Type</th>
                    <th>ID Usertour.io</th>
                    <th>URL</th>
                    <th>Portée</th>
                    <th>Points</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<div class="modal fade onboarding_step_modal" tabindex="-1" role="dialog"></div>