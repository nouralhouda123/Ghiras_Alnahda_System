<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $casts = [
        'is_anonymous' => 'boolean',
    ];

    public static function getSensitivityMetaData(): array
    {



        return [
            'level_1' => [
                'label' => 'Level 1 — General Support',
                'target_role' => 'support_team',
                'examples' => 'Technical issues, course registration problems, attendance inquiries.',
                'allow_anonymous' => false
            ],
            'level_2' => [
                'label' => 'Level 2 — Department Management',
                'target_role' => 'department_manager',
                'examples' => 'Problems with supervisors, unfair evaluation, internal department conflicts.',
                'allow_anonymous' => false
            ],
            'level_3' => [
                'label' => 'Level 3 — Confidential',
                'target_role' => 'general_manager',
                'examples' => 'Harassment, abuse of authority, threats, privacy violations.',
                'allow_anonymous' => true
            ]
        ];

    }
    public function scopeWithControlPermission(Builder $query): Builder
    {
        $user = auth()->user();

        /** @var \App\Models\User $user */
        // 1. الإدارة العليا ترى كافة المستويات
        if ($user->hasRole('general_manager')) {
            return $query;
        }

        // 2. مدير القسم يرى المستوى الأول والثاني فقط
        if ($user->hasRole('department_manager')) {
            return $query->whereIn('sensitivity_level', ['level_1', 'level_2']);
        }

        // 3. فريق الدعم يرى المستوى الأول فقط لحلها
        if ($user->hasRole('support_team')) {
            return $query->where('sensitivity_level', 'level_1');
        }

        // 4. إذا كان مستخدماً عادياً (متطوع) يرى فقط شكاويه الشخصية المفتوحة باسمه
        return $query->where('user_id', $user->id)->where('is_anonymous', false);
    }

    /**
     * Relationship: الشكوى تنتمي لمستخدم (المشتكي) - Nullable في حال كانت مجهولة
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: الشكوى تنتمي لموظف معين تم تعيينه لحلها
     */
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}
