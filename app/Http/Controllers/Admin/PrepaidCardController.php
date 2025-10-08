<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryMan;
use App\Models\PrepaidCard;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use RandomLib\Factory;
use Rap2hpoutre\FastExcel\FastExcel;

class PrepaidCardController extends Controller
{
    public function index(Request $request)
    {
        $card_no = $request->query('card_no');

        $query = PrepaidCard::with('usable');

        if ($card_no != null) {
            $query = $query->where('card_no', $card_no)
                ->orWhere('card_no', 'like', "%{$card_no}$");
        }

        $prepaid_cards = $query->paginate(config('default_pagination'));

        return view('admin-views.delivery-man.prepaid-cards.index', compact('prepaid_cards'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'price' => 'required|numeric|min:10',
            'duration_days' => 'required|integer|min:1',
            'count' => 'required|integer|min:1',
        ]);

        $factory = new Factory;
        $lowGenerator = $factory->getLowStrengthGenerator();
        $mediumGenerator = $factory->getMediumStrengthGenerator();
        $characters = '0123456789';
        $now = now();

        $count = $validated['count'];

        $data = new Collection;

        for ($x = 0; $x < $count; $x++) {
            $generatedCardNo = $mediumGenerator->generateString(10, $characters);
            while (PrepaidCard::where('card_no', $generatedCardNo)->exists()) {
                $generatedCardNo = $mediumGenerator->generateString(10, $characters);
            }

            $pin = $lowGenerator->generateString(6, $characters);

            $data->push([
                'card_no' => $generatedCardNo,
                'price' => $validated['price'],
                'duration_days' => $validated['duration_days'],
                'pin' => $pin,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        PrepaidCard::insert($data->toArray());

        return back();
    }

    public function destroy(PrepaidCard $prepaidCard)
    {
        $prepaidCard->delete();
        return back();
    }

    public function export(Request $request)
    {
        $validated = $request->validate(['format' => 'required|string|in:xlsx,csv']);
        $format = $validated['format'];

        $prepaidCards = PrepaidCard::with('usable')->get();
        $export = new FastExcel($this->format_export_data($prepaidCards));

        if ($format == 'csv') {
            return $export->download('prepaid_cards.csv');
        }

        return $export->download('prepaid_cards.xlsx');
    }

    private function format_export_data($prepaidCards)
    {
        $data = [];
        foreach ($prepaidCards as $key => $prepaidCard) {

            $data[] = [
                '#' => $key + 1,
                translate('messages.card_no') => $prepaidCard->card_no,
                translate('messages.price') => \App\CentralLogics\Helpers::format_currency($prepaidCard->price),
                translate('messages.duration_days') => $prepaidCard->duration_days,
                translate('messages.pin') => $prepaidCard->pin,
                translate('messages.used') => $prepaidCard->is_used ? 'Yes' : 'No',
                translate('messages.used_by') => $prepaidCard->usable instanceof DeliveryMan ?
                    $prepaidCard->usable->f_name . ' ' . $prepaidCard->usable->l_name :
                    'None',
                translate('messages.date') => date('d M Y', strtotime($prepaidCard->created_at)),
            ];
        }
        return $data;
    }
}
