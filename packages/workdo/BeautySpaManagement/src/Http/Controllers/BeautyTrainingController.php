<?php

namespace Workdo\BeautySpaManagement\Http\Controllers;

use Workdo\BeautySpaManagement\Http\Requests\StoreTrainingRequest;
use Workdo\BeautySpaManagement\Http\Requests\UpdateTrainingRequest;
use Workdo\BeautySpaManagement\Models\BeautyTraining;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Workdo\BeautySpaManagement\Events\CreateBeautyTraining;
use Workdo\BeautySpaManagement\Events\DestroyBeautyTraining;
use Workdo\BeautySpaManagement\Events\UpdateBeautyTraining;

class BeautyTrainingController extends Controller
{
    public function index()
    {
        if(Auth::user()->can('manage-beauty-trainings')){
            $trainings = BeautyTraining::query()

                ->where(function($q) {
                    if(Auth::user()->can('manage-any-beauty-trainings')) {
                        $q->where('created_by', creatorId());
                    } elseif(Auth::user()->can('manage-own-beauty-trainings')) {
                        $q->where('creator_id', Auth::id());
                    } else {
                        $q->whereRaw('1 = 0');
                    }
                })
                ->when(request('training_name'), function($q) {
                    $q->where(function($query) {
                    $query->where('training_name', 'like', '%' . request('training_name') . '%');
                    $query->orWhere('trainer', 'like', '%' . request('training_name') . '%');
                    $query->orWhere('location', 'like', '%' . request('training_name') . '%');
                    });
                })

                ->when(request('sort'), fn($q) => $q->orderBy(request('sort'), request('direction', 'asc')), fn($q) => $q->latest())
                ->paginate(request('per_page', 10))
                ->withQueryString();

            return Inertia::render('BeautySpaManagement/Trainings/Index', [
                'trainings' => $trainings,

            ]);
        }
        else{
            return back()->with('error', __('Permission denied'));
        }
    }

    public function store(StoreTrainingRequest $request)
    {
        if(Auth::user()->can('create-beauty-trainings')){
            $validated = $request->validated();

            $training                = new BeautyTraining();
            $training->training_name = $validated['training_name'];
            $training->trainer       = $validated['trainer'];
            $training->date          = $validated['date'];
            $training->duration      = $validated['duration'];
            $training->location      = $validated['location'];
            $training->description   = $validated['description'];

            $training->creator_id = Auth::id();
            $training->created_by = creatorId();
            $training->save();
            CreateBeautyTraining::dispatch($request, $training);

            return redirect()->route('beauty-spa-management.trainings.index')->with('success', __('The training has been created successfully.'));
        }
        else{
            return redirect()->route('beauty-spa-management.trainings.index')->with('error', __('Permission denied'));
        }
    }

    public function update(UpdateTrainingRequest $request, BeautyTraining $training)
    {
        if(Auth::user()->can('edit-beauty-trainings')){
            $validated = $request->validated();



            $training->training_name = $validated['training_name'];
            $training->trainer       = $validated['trainer'];
            $training->date          = $validated['date'];
            $training->duration      = $validated['duration'];
            $training->location      = $validated['location'];
            $training->description   = $validated['description'];

            $training->save();
            UpdateBeautyTraining::dispatch($request, $training);


            return redirect()->back()->with('success', __('The training details are updated successfully.'));
        }
        else{
            return redirect()->route('beauty-spa-management.trainings.index')->with('error', __('Permission denied'));
        }
    }

    public function destroy(BeautyTraining $training)
    {
        if(Auth::user()->can('delete-beauty-trainings')){
            DestroyBeautyTraining::dispatch($training);

            $training->delete();

            return redirect()->back()->with('success', __('The training has been deleted.'));
        }
        else{
            return redirect()->route('beauty-spa-management.trainings.index')->with('error', __('Permission denied'));
        }
    }




}