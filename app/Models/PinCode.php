<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PinCode extends Model
{
    protected $fillable = ['code', 'area', 'post_office', 'city', 'state'];

    public function fullLabel(): string
    {
        $office = $this->post_office ? " ({$this->post_office})" : '';

        return "{$this->area}{$office}, {$this->city} - {$this->code}";
    }
}
