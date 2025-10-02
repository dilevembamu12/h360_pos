// File: Modules/Hospital/Entities/Message.php

namespace Modules\Hospital\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// Assurez-vous que App\Models\User et App\Models\BusinessLocation sont les bons namespaces dans votre application H360🛒POS
use App\Models\User; // Assuming User model is in App\Models
use App\Models\BusinessLocation; // Assuming BusinessLocation model is in App\Models


class Message extends Model
{
    use HasFactory; // Pas de SoftDeletes pour cette table selon la migration

    protected $table = 'hospital_messages';

    protected $fillable = [
        'business_location_id',
        'sender_user_id',
        'receiver_user_id',
        'subject',
        'body',
        'sent_at',
        'read_at',
        'is_read',
        // 'conversation_id', // Commenté car pas de FK explicite dans la migration fournie pour l'instant
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'read_at' => 'datetime',
        'is_read' => 'boolean',
    ];

    /**
     * Get the hospital (business location) this message belongs to.
     */
    public function businessLocation()
    {
         // Assurez-vous que 'App\Models\BusinessLocation' est le modèle correct pour votre application
        return $this->belongsTo(BusinessLocation::class, 'business_location_id');
    }


    /**
     * Get the sender of the message.
     */
    public function sender()
    {
        // Assurez-vous que 'App\Models\User' est le modèle correct pour votre application
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    /**
     * Get the receiver of the message.
     */
    public function receiver()
    {
         // Assurez-vous que 'App\Models\User' est le modèle correct pour votre application
        return $this->belongsTo(User::class, 'receiver_user_id');
    }

    // Si une table 'hospital_conversations' est créée, ajoutez la relation belongsTo
    // public function conversation()
    // {
    //     return $this->belongsTo(Conversation::class, 'conversation_id');
    // }

    // Si vous utilisez des factories
    // protected static function newFactory()
    // {
    //     return \Modules\Hospital\Database\Factories\MessageFactory::new();
    // }
}