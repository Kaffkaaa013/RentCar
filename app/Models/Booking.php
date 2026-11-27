<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bookings';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'car_id',
        'start_date',
        'end_date',
        'total_days',
        'total_price',
        'booking_status',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'total_days' => 'integer',
            'total_price' => 'decimal:2',
        ];
    }

    /**
     * Customer who made the booking.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Booked car.
     *
     * @return BelongsTo<Car, $this>
     */
    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    /**
     * Payment record for this booking.
     *
     * @return HasOne<Payment, $this>
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Return record for this booking.
     *
     * @return HasOne<CarReturn, $this>
     */
    public function carReturn(): HasOne
    {
        return $this->hasOne(CarReturn::class, 'booking_id');
    }

    /**
     * Review for this booking.
     *
     * @return HasOne<Review, $this>
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Hitung jumlah hari sewa (minimal 1 hari).
     */
    public static function calculateDays(string $startDate, string $endDate): int
    {
        $days = (int) Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));

        return max(1, $days);
    }

    /**
     * Periksa apakah mobil sudah dipesan pada rentang tanggal yang bertabrakan.
     */
    public static function hasDateConflict(int $carId, string $startDate, string $endDate, ?int $ignoreBookingId = null): bool
    {
        return static::query()
            ->where('car_id', $carId)
            ->whereIn('booking_status', ['pending', 'confirmed', 'ongoing'])
            ->when($ignoreBookingId, fn ($q) => $q->where('id', '!=', $ignoreBookingId))
            ->where(function ($query) use ($startDate, $endDate): void {
                $query->where('start_date', '<=', $endDate)
                    ->where('end_date', '>=', $startDate);
            })
            ->exists();
    }
}
