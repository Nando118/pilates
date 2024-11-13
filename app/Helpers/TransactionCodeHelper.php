<?php

namespace App\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionCodeHelper
{
    public static function generateTransactionCode()
    {
        // Mendapatkan tahun dan bulan saat ini
        $yearMonth = Carbon::now()->format('Ym');

        // Memulai transaksi database
        DB::beginTransaction();

        try {
            // Mendapatkan nomor ticket terakhir untuk bulan dan tahun saat ini dengan lock untuk mencegah pembacaan ganda
            $lastTransactionCode = DB::table('credit_transactions')
                ->where('created_at', '>=', Carbon::now()->startOfMonth())
                ->where('created_at', '<=', Carbon::now()->endOfMonth())
                ->lockForUpdate()
                ->max('transaction_code');

            // Jika tidak ada nomor ticket untuk bulan dan tahun saat ini, set nomor ticket pertama menjadi 1
            if (!$lastTransactionCode) {
                $newTransactionCode = 1;
            } else {
                // Increment nomor ticket
                $newTransactionCode = (int) substr($lastTransactionCode, -6) + 1; // Increment nomor ticket
            }

            // Commit transaksi jika berhasil
            DB::commit();
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi kesalahan
            DB::rollback();
            throw $e;
        }

        // Format nomor ticket dengan padding 6 digit
        $formattedTransactionCode = str_pad($newTransactionCode, 6, '0', STR_PAD_LEFT);

        // Generate nomor ticket dengan format yang diinginkan
        $transactionCode = 'OHN-TXN-' . $yearMonth . '-' . $formattedTransactionCode;

        return $transactionCode;
    }
}
