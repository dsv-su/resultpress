<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable = [
        'name',
        'value',
        'type',
    ];

    /**
     * Get field type enum values
     *
     * @return array
     */
    public static function getFieldTypes(){
        return [
            'text' => 'Text',
            'textarea' => 'Textarea',
            'image' => 'Image',
            'file' => 'File',
            // 'select' => 'Select',
            // 'checkbox' => 'Checkbox',
            // 'radio' => 'Radio',
            // 'email' => 'Email',
            // 'password' => 'Password',
            // 'number' => 'Number',
            'date' => 'Date',
            'time' => 'Time',
            'datetime' => 'Datetime',
            // 'color' => 'Color',
            // 'range' => 'Range',
            // 'url' => 'Url',
            // 'tel' => 'Tel',
            // 'search' => 'Search',
            // 'hidden' => 'Hidden',
            // 'month' => 'Month',
            // 'week' => 'Week',
            // 'currency' => 'Currency',
            // 'language' => 'Language',
            // 'country' => 'Country',
            // 'timezone' => 'Timezone',
            'html' => 'Html',
            // 'markdown' => 'Markdown',
            'wysiwyg' => 'Wysiwyg',
            'code' => 'Code',
            'json' => 'Json',
            'key-value' => 'Key Value',
        ];
    }
}
