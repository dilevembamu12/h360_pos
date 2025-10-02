{{--
    Cette vue partielle est destinée à être incluse dans d'autres vues (comme reception.index)
    pour afficher le contenu d'une file d'attente.
    Elle assume qu'une variable $mainQueue (ou similaire) est passée, qui est une instance du modèle Queue.
--}}

@if($mainQueue ?? null) {{-- Vérifie si la variable $mainQueue existe et n'est pas nulle --}}
    @if($mainQueue->items->isEmpty()) {{-- Vérifie si la file d'attente est vide --}}
        <p>Aucun patient en attente dans cette file d'attente.</p>
    @else
        <ul class="list-group"> {{-- Utilisation de classes Bootstrap list-group --}}
            @foreach($mainQueue->items->sortBy('order') as $item) {{-- Trie les éléments par leur ordre --}}
                {{-- N'affiche que les éléments qui sont en statut 'waiting' ou 'serving' --}}
                @if(in_array($item->status, ['waiting', 'serving']))
                    <li class="list-group-item d-flex justify-content-between align-items-center
                                {{ $item->status == 'serving' ? 'list-group-item-warning' : '' }}"> {{-- Met en évidence si 'serving' --}}
                        <div>
                            {{-- Affichage de l'ordre ou du numéro dans la file (si disponible) --}}
                            #{{ $loop->iteration }} -
                            {{-- Affichage du nom du patient --}}
                            <strong>Patient:</strong> {{ $item->patient->full_name ?? $item->patient->first_name . ' ' . $item->patient->last_name }}
                            {{-- Affichage du service lié (si disponible) --}}
                            @if($item->service)
                                <br><small>Service demandé: {{ $item->service->name }}</small>
                            @endif
                            {{-- Affichage de la visite liée (si disponible) --}}
                             @if($item->visit)
                                <br><small>Visite #{{ $item->visit->id }}</small>
                            @endif
                        </div>
                        <div>
                            {{-- Affichage du statut --}}
                            <span class="badge {{ $item->status == 'serving' ? 'badge-warning' : 'badge-info' }} badge-pill mr-2">
                                {{ $item->status == 'waiting' ? 'En attente' : 'En cours' }}
                            </span>
                            {{-- Boutons d'action --}}
                            {{-- Bouton "Appeler" visible si le statut est 'waiting' --}}
                            @if($item->status == 'waiting')
                                <button class="btn btn-sm btn-success mr-2" onclick="callQueueItem({{ $item->id }})">Appeler</button>
                            @endif
                             {{-- Bouton "Servi" visible si le statut est 'waiting' ou 'serving' --}}
                             @if(in_array($item->status, ['waiting', 'serving']))
                                <button class="btn btn-sm btn-primary mr-2" onclick="markAsServedQueueItem({{ $item->id }})">Marquer Servi</button>
                             @endif
                             {{-- Bouton "Annuler" --}}
                            <button class="btn btn-sm btn-danger" onclick="cancelQueueItem({{ $item->id }})">Annuler</button>
                        </div>
                    </li>
                @endif {{-- Fin de la condition sur le statut --}}
            @endforeach {{-- Fin de la boucle sur les éléments de la file --}}
        </ul>

        {{-- TODO: Ajouter la pagination si la file peut être très longue --}}

    @endif {{-- Fin de la condition ismpty() --}}

@else {{-- Si $mainQueue n'est pas définie ou est nulle --}}
    <p>Aucune file d'attente spécifiée ou configurée pour l'affichage.</p>
@endif {{-- Fin de la condition $mainQueue ?? null --}}

{{-- Scripts JavaScript pour les actions (nécessitent AJAX) --}}
@push('scripts')
<script>
    // Fonction pour appeler un élément de la file d'attente
    function callQueueItem(itemId) {
        console.log('Appeler l\'élément de file d\'attente :', itemId);
        // TODO: Implémenter la requête AJAX pour mettre à jour le statut à 'serving'
        // et potentiellement déclencher l'affichage sur un écran public.
        // Exemple (utilisation de Fetch API) :
        // fetch(`/hopital/queue/${itemId}/call`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
        //     .then(response => response.json())
        //     .then(data => {
        //         if (data.success) {
        //             alert('Patient appelé.');
        //             refreshQueue(); // Actualiser l'affichage
        //         } else {
        //             alert('Erreur lors de l\'appel.');
        //         }
        //     })
        //     .catch(error => console.error('Erreur:', error));
        alert('Action Appeler pour l\'élément ' + itemId + ' en cours...'); // Message temporaire
    }

    // Fonction pour marquer un élément de la file d'attente comme servi
    function markAsServedQueueItem(itemId) {
         console.log('Marquer comme servi l\'élément de file d\'attente :', itemId);
         // TODO: Implémenter la requête AJAX pour mettre à jour le statut à 'served'
         // et potentiellement le retirer de l'affichage principal de la file.
         // Exemple (utilisation de Fetch API) :
        // fetch(`/hopital/queue/${itemId}/serve`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
        //     .then(response => response.json())
        //     .then(data => {
        //         if (data.success) {
        //             alert('Patient marqué comme servi.');
        //             refreshQueue(); // Actualiser l'affichage
        //         } else {
        //             alert('Erreur lors du marquage.');
        //         }
        //     })
        //     .catch(error => console.error('Erreur:', error));
        alert('Action Marquer Servi pour l\'élément ' + itemId + ' en cours...'); // Message temporaire
    }

    // Fonction pour annuler un élément de la file d'attente
    function cancelQueueItem(itemId) {
        console.log('Annuler l\'élément de file d\'attente :', itemId);
        // TODO: Implémenter la requête AJAX pour mettre à jour le statut à 'cancelled'
        // et le retirer de l'affichage principal.
         // Exemple (utilisation de Fetch API) :
        // if (confirm('Êtes-vous sûr de vouloir annuler cet élément de la file ?')) {
        //     fetch(`/hopital/queue/${itemId}/cancel`, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
        //         .then(response => response.json())
        //         .then(data => {
        //             if (data.success) {
        //                 alert('Élément annulé.');
        //                 refreshQueue(); // Actualiser l'affichage
        //             } else {
        //                 alert('Erreur lors de l\'annulation.');
        //             }
        //         })
        //         .catch(error => console.error('Erreur:', error));
        // }
         alert('Action Annuler pour l\'élément ' + itemId + ' en cours...'); // Message temporaire
    }

    // Note : La fonction `refreshQueue()` doit être définie dans la vue parente (ex: index.blade.php)
    // ou dans un script global accessible.
</script>
@endpush