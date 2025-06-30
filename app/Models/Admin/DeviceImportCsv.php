<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class DeviceImportCsv extends Model
{
    /* Table */
    protected $table = 'device_import_csv';
    /* Fillable */
    protected $fillable = [
        'file_name',
        'file_path',
        'user_id',
        'total',
        'inserted',
        'skipped',
        'updated',
        'active',
        'service_provider_id'
    ];
}
