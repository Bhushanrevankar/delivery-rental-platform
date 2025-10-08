<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use RandomLib\Factory;

class PrepaidCard extends Model
{
    protected $fillable = ['card_no', 'price', 'duration_days', 'pin'];

    protected $attributes = ['is_used' => false];

    protected $casts = [
        'price' => 'double',
        'is_used' => 'boolean',
    ];

    protected $hidden = ['pin'];

    public static function boot()
    {
        parent::boot();
        // This only works in single model insert.
        self::creating(function ($model) {
            if ($model->card_no == null) {
                $model->setCardNo();
            }
            if ($model->pin == null) {
                $model->setPin();
            }
        });
    }

    public function usable(): MorphTo
    {
        return $this->morphTo();
    }

    // generate and set card_no
    public function setCardNo()
    {
        $factory = new Factory();
        $randGenerator = $factory->getMediumStrengthGenerator();
        $card_no = $randGenerator->generateString(10, '0123456789');

        // Implemented database recursion
        while ($this->cardNoExists($card_no)) {
            $card_no = $randGenerator->generateString(10, '0123456789');
        }

        $this->card_no = $card_no;
    }

    private function cardNoExists(string $card_no): bool
    {
        $card = PrepaidCard::where('card_no', $card_no)->first();
        return $card != null;
    }

    public function setPin()
    {
        $factory = new Factory();
        $randGenerator = $factory->getLowStrengthGenerator();
        $pin = $randGenerator->generateString(6, '0123456789');
        $this->pin = $pin;
    }
}
