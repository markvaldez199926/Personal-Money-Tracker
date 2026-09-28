<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'currency',
        'currency_symbol',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function wallets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Wallet::class);
    }

    public function categories(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function transactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function budgets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Budget::class);
    }

    public function recurringTransactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(RecurringTransaction::class);
    }

    public const SUPPORTED_CURRENCIES = [
        'PHP' => ['code' => 'PHP', 'symbol' => '₱', 'name' => 'Philippine Peso', 'flag' => '🇵🇭'],
        'USD' => ['code' => 'USD', 'symbol' => '$', 'name' => 'US Dollar', 'flag' => '🇺🇸'],
        'EUR' => ['code' => 'EUR', 'symbol' => '€', 'name' => 'Euro', 'flag' => '🇪🇺'],
        'GBP' => ['code' => 'GBP', 'symbol' => '£', 'name' => 'British Pound', 'flag' => '🇬🇧'],
        'JPY' => ['code' => 'JPY', 'symbol' => '¥', 'name' => 'Japanese Yen', 'flag' => '🇯🇵'],
        'CAD' => ['code' => 'CAD', 'symbol' => 'CA$', 'name' => 'Canadian Dollar', 'flag' => '🇨🇦'],
        'AUD' => ['code' => 'AUD', 'symbol' => 'AU$', 'name' => 'Australian Dollar', 'flag' => '🇦🇺'],
        'SGD' => ['code' => 'SGD', 'symbol' => 'S$', 'name' => 'Singapore Dollar', 'flag' => '🇸🇬'],
    ];

    public function setCurrency(string $code): bool
    {
        $code = strtoupper($code);
        if (isset(self::SUPPORTED_CURRENCIES[$code])) {
            $this->currency = $code;
            $this->currency_symbol = self::SUPPORTED_CURRENCIES[$code]['symbol'];
            return $this->save();
        }
        return false;
    }

    public function formatMoney(float|int|string|null $amount): string
    {
        $num = (float) ($amount ?? 0);
        $symbol = $this->currency_symbol ?: (self::SUPPORTED_CURRENCIES[$this->currency]['symbol'] ?? '₱');
        return $symbol . number_format($num, 2);
    }
}
