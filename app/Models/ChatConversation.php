<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatConversation extends Model
{
    protected $fillable = [
        'title',
        'pengajuan_id',
        'type',
        'participants',
        'last_message_at'
    ];

    protected $casts = [
        'participants' => 'array',
        'last_message_at' => 'datetime'
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }

    public function pengajuan(): BelongsTo
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function lastMessage()
    {
        return $this->hasOne(ChatMessage::class, 'conversation_id')->latest();
    }

    public function getParticipantUsers()
    {
        return User::whereIn('id', $this->participants)->get();
    }

    public function hasParticipant($userId): bool
    {
        return in_array($userId, $this->participants);
    }

    public function addParticipant($userId): self
    {
        $participants = $this->participants;
        if (!in_array($userId, $participants)) {
            $participants[] = $userId;
            $this->update(['participants' => $participants]);
        }
        return $this;
    }
}
