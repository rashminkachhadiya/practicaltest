<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Price;
use App\Models\Simulation;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use DB;

class PriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $allLabels = Price::pluck('name')->toArray();
        $probability = Price::pluck('probability_percentage')->toArray();
        $actual = Price::pluck('current_distribution_percentage')->toArray();
        $data = ['labels' => $allLabels,
            'probability' => $probability,
            'actual' => $actual
        ];
        return view('price.index', compact('data'));   
    }

    public function getAll(Request $request)
    {
      $prices = Price::select('prices.*');
      return Datatables::of($prices)
        ->addColumn('action', function ($prices) {
           $html = '<div class="btn-group">';
           $html .= '<a href="price/'. $prices->id .'/edit" class="btn btn-xs btn-info mr-1 edit" title="Edit"><i class="fa fa-edit"></i> </a>';
              $html .= '<a data-toggle="tooltip" id="' . $prices->id . '" class="btn btn-xs btn-danger mr-1 delete" title="Delete"><i class="fa fa-trash"></i> </a>';
           $html .= '</div>';
           return $html;
        })
        ->rawColumns(['action'])
        ->addIndexColumn()
        ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('price.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'probability_percentage' => 'required|numeric|min:0|max:100',
        ])->after(function ($validator) use ($data) {
            $total_percentage = Price::sum('probability_percentage');
            if ($total_percentage + $data['probability_percentage'] > 100) {
                $validator->errors()->add('probability_percentage', 'Total probability cannot exceed 100%.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }else{
            DB::beginTransaction();
            try {

               $price = new Price();
               $price->name = $data['name'];
               $price->probability_percentage = $data['probability_percentage'];
               $price->save();
               
               DB::commit();
               return redirect('/price')->with('success', 'Price Added successfully');

            } catch (\Exception $e) {
               DB::rollback();
               return response()->json(['type' => 'error', 'message' => $e->getMessage()]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $price = Price::findOrFail($id);
        return view('price.edit', ['price' => $price]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'probability_percentage' => 'required|numeric|min:0|max:100',
        ])->after(function ($validator) use ($data,$id) {
            $total_percentage = Price::where('id','!=',$id)->sum('probability_percentage');
            $maximumPro = Price::where('id','=',$id)->sum('probability_percentage');
            $val = 100 - $total_percentage;
            if ($total_percentage + $data['probability_percentage'] > 100) {
                $validator->errors()->add('probability_percentage', "You can add maximum probability $val ");
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }else{
            DB::beginTransaction();
            try {

               $price = Price::find($id);
               $price->name = $data['name'];
               $price->probability_percentage = $data['probability_percentage'];
               $price->save();
               
               DB::commit();
               return redirect('/price')->with('success', 'Price updated successfully');

            } catch (\Exception $e) {
               DB::rollback();
               return response()->json(['type' => 'error', 'message' => $e->getMessage()]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $price = Price::findOrFail($id);
        $price->delete();
        return response()->json(['type' => 'success', 'message' => "Successfully Deleted"]);
    }

    public function simulate(Request $request)
    {
        Simulation::truncate();
        Price::query()->update(['count_awarded' => 0, 'current_distribution_percentage' => 0]);
        $prizes = Price::all();
        $outcome = [];
        foreach ($prizes as $prize) {
            $outcome[$prize->name] = 0;
        }

        for ($i = 0; $i < $request->num_entries; $i++) {
            $random = mt_rand(1, 10000) / 100; // 0-100 random number with two decimal precision
            $accumulatedProbability = 0;
            foreach ($prizes as $prize) {
                $accumulatedProbability += $prize->probability_percentage;
                if ($random <= $accumulatedProbability) {
                    $outcome[$prize->name]++;
                    $prize->count_awarded++;
                    break;
                }
            }
        }

        // Update current distribution percentages
        $totalAwarded = array_sum($outcome);
        foreach ($prizes as $prize) {
            $prize->current_distribution_percentage = ($prize->count_awarded / $totalAwarded) * 100;
            $prize->save();
        }

        // Store simulation result
        Simulation::create([
            'num_entries' => $request->num_entries,
            'outcome' => json_encode($outcome),
        ]);

        return redirect('/price');
    }

    public function reset()
    {
        Simulation::truncate();
        Price::query()->update(['count_awarded' => 0, 'current_distribution_percentage' => 0]);
        return redirect('/price');
    }
}
