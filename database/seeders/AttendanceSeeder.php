<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $photo = 'https://i.pinimg.com/736x/6f/4d/bb/6f4dbb317dff5f8aff6828609dcd8243.jpg';

        $today    = Carbon::today();

        $data = [
            // ── User 2, Hari ini, Kunjungan 1 ──
            [
                'user_id'                  => 2,
                'attendance_date'          => $today->format('Y-m-d'),
                'checkin_time'             => $today->copy()->setTime(8, 15),
                'checkin_latitude'         => '-6.181510992757831',
                'checkin_longitude'        => '106.89106034008041',
                'checkin_photo'            => $photo,
                'store_name'               => 'Toko Maju Jaya',
                'person_in_charge_name'    => 'Ahmad Yani',
                'person_in_charge_phone'   => '081234567890',
                'checkout_time'            => $today->copy()->setTime(9, 45),
                'checkout_latitude'        => '-6.181510992757831',
                'checkout_longitude'       => '106.89106034008041',
                'checkout_photo'           => $photo,
                'work_duration_minutes'    => 90,
                'created_at'               => now(),
                'updated_at'               => now(),
            ],

            // ── User 2, Hari ini, Kunjungan 2 ──
            [
                'user_id'                  => 2,
                'attendance_date'          => $today->format('Y-m-d'),
                'checkin_time'             => $today->copy()->setTime(10, 30),
                'checkin_latitude'         => '-6.182321642781745',
                'checkin_longitude'        => '106.90453575749939',
                'checkin_photo'            => $photo,
                'store_name'               => 'Indomaret Cipete Raya',
                'person_in_charge_name'    => 'Budi Kurniawan',
                'person_in_charge_phone'   => '082198765432',
                'checkout_time'            => $today->copy()->setTime(12, 0),
                'checkout_latitude'        => '-6.182321642781745',
                'checkout_longitude'       => '106.90453575749939',
                'checkout_photo'           => $photo,
                'work_duration_minutes'    => 90,
                'created_at'               => now(),
                'updated_at'               => now(),
            ],

            // ── User 2, Keesokan harinya, Kunjungan 1 (masih di lapangan) ──
            [
                'user_id'                  => 2,
                'attendance_date'          => $today->format('Y-m-d'),
                'checkin_time'             => $today->copy()->setTime(13, 0),
                'checkin_latitude'         => '-6.119863600096797',
                'checkin_longitude'        => '106.87096430207403',
                'checkin_photo'            => $photo,
                'store_name'               => 'Alfamart Fatmawati',
                'person_in_charge_name'    => 'Suharto',
                'person_in_charge_phone'   => '081311223344',
                'checkout_time'            => null,
                'checkout_latitude'        => null,
                'checkout_longitude'       => null,
                'checkout_photo'           => null,
                'work_duration_minutes'    => null,
                'created_at'               => now(),
                'updated_at'               => now(),
            ],

            // ── User 3, Hari ini, Kunjungan 1 ──
            [
                'user_id'                  => 3,
                'attendance_date'          => $today->format('Y-m-d'),
                'checkin_time'             => $today->copy()->setTime(7, 45),
                'checkin_latitude'         => '-6.153171174651185',
                'checkin_longitude'        => '106.7963916332887',
                'checkin_photo'            => $photo,
                'store_name'               => 'Toko Berkah Mandiri',
                'person_in_charge_name'    => 'Pak Joko',
                'person_in_charge_phone'   => '085766554433',
                'checkout_time'            => $today->copy()->setTime(9, 30),
                'checkout_latitude'        => '-6.153171174651185',
                'checkout_longitude'       => '106.7963916332887',
                'checkout_photo'           => $photo,
                'work_duration_minutes'    => 105,
                'created_at'               => now(),
                'updated_at'               => now(),
            ],

            // ── User 3, Hari ini, Kunjungan 2 (masih di lapangan) ──
            [
                'user_id'                  => 3,
                'attendance_date'          => $today->format('Y-m-d'),
                'checkin_time'             => $today->copy()->setTime(10, 15),
                'checkin_latitude'         => '-6.185573200810717',
                'checkin_longitude'        => '106.7637677940942',
                'checkin_photo'            => $photo,
                'store_name'               => 'Minimarket Sejahtera',
                'person_in_charge_name'    => 'Ibu Rina',
                'person_in_charge_phone'   => '087812345678',
                'checkout_time'            => null,
                'checkout_latitude'        => null,
                'checkout_longitude'       => null,
                'checkout_photo'           => null,
                'work_duration_minutes'    => null,
                'created_at'               => now(),
                'updated_at'               => now(),
            ],
        ];

        DB::table('attendances')->insert($data);
    }
}