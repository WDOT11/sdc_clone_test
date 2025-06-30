<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = [
        'name',
        'org_slug',
        'org_link',
        'cover_image_media_id',
        'org_logo_media_id',
        'repair_enabled',
        'can_edit_device',
        'service_agreement_media_id',
        'agreement_link',
        'allow_parents_claim',
        'org_type',
        'portal_status',
        'close_portal_message',
        'enable_multiple_sub_org',
        'additional_instructions',
        'service_provider_id',
        'setup_steps'
    ];
}