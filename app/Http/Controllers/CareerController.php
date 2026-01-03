<?php

namespace App\Http\Controllers;

use App\Http\Resources\CareerCollection;
use App\Http\Resources\CareerResource;
use App\Models\Career;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCareerRequest;
use App\Http\Requests\UpdateCareerRequest;
use App\Jobs\SendJobApplication;
use App\Models\User;
use Exception;

/**
 * @group Careers management (JOBS)
 *
 * APIs for uploading and managing careers
 * it contains the following endpoints:
 * - Create Job
 * - Get one Job
 * - Get all jobs
 * - Delete Job
 * - Update Job
 * - Search Jobs
 * - Toggle job on or off
 * - Get deleted jobs
 * - Restore job
 * - Destroy job(Permanent Delete)
 * 
 */
class CareerController extends Controller
{
    /**
     * Display all the careers
     * 
     * Get all the careers
     */
    public function index()
    {
        return new CareerCollection(Career::where('is_active', true)->paginate(15));
    }

   

    /**
     * create a new job
     */
    public function store(StoreCareerRequest $request)
    {
        $this->authorize('create', Career::class);

        $admin = auth('admin')->user();
        // dd($admin);
        $newJob = $admin->careers()->create($request->validated());
        return new CareerResource($newJob);
    }

    /**
     * Get one job
     */
    public function show(Career $career)
    {
        return new CareerResource($career);
    }

    
    /**
     * Update a job
     * 
     * this endpoint supports both put and patch requests
     */
    public function update(UpdateCareerRequest $request, Career $career)
    {

        $this->authorize('update', $career);

        $admin = auth('sanctum')->user();

        $updated = $admin->careers()->update($request->validated());
        
        return new CareerResource($updated);
    }

    /**
     * Soft delete a career
     */
    public function delete(Career $career)
    {

        $this->authorize('delete', $career);


        $career->delete();
        return response()->json(['message' => "Career deleted successfully"]);
    }

     /**
     *  
     * Search for a job
     * 
     * @queryparam search string The search term, the search is done on the title of the job by default, 
     * 
     * @queryparam column string The column to search on. Optional. default: title
     * if you have other columns that you want to search on, you can add them as query parameters
     * allowed columns are: title, location, type
     * if using the type query key, the allowed values are: 'full-time','part-time','contract', 'internship'
     * 
     * 
     * @unauthenticated
     */
    public function search(Request $request){

         $allowedColumns = ['title', 'location', 'type'];

        $column = $request->input('column', 'title');

      if (!in_array($column, $allowedColumns)) {
            $column = 'title';
        }

        $search = $request->get('search');

        //add more where clauses as required
        $career = Career::where('title', 'LIKE', '%'. $search .'%')->paginate(5);
        return CareerResource::collection($career);
    }

    /**
     * Toggle a career on or off
     */

    public function careerToggle(Career $career){

        $this->authorize('update', $career);

        $career->is_active = !$career->is_active;
        $career->save();

        return new CareerResource($career);
    }

    /**
     * restore a soft deleted career
     * 
     * this endpoint takes the id of the career intended to be restored
     * you can get the id from the data restored from the get-deleted endpoint
     */

    public function restore(Career $career){

        $this->authorize('restore', $career);

        $career = Career::withTrashed()->findOrFail($career->id);
        $career->restore();
        $career->refresh();

        return response()->json([
            'status' => true,
            'message' => 'Career restored successfully',
            'career' =>  new CareerResource($career)

        ]);
    }
    
     /**
     * get all deleted careers
     * 
     * this endpoint returns all the careers that have been soft deleted
     */
    public function getDeleted(){
        return new CareerCollection(Career::onlyTrashed()->get());
    }

   

    /**
     * Permanently delete a career
     * 
     * this endpoint is to permanently delete a career
     */
    public function destroy(Career $career){

        $this->authorize('forceDelete', $career);

        Career::find($career->id)->forceDelete();
        return response()->json(['message' => "Career deleted successfully"]);
    }
    

    /**
     * Send a job application
     * 
     * this endpoint is to send a job application
     * 
     * @bodyParam name string required The name of the person applying for the job
     * @bodyParam email string required The email of the person applying for the job
     * @bodyParam phoneNumber string required The phone number of the person applying for the job
     * @bodyParam jobTitle string required The title of the job
     * @bodyParam applicantMessage string required The description/cover letter/ brief summary of the person applying for the job
     * @bodyParam resume file required The resume of the person applying for the job
     */
    public function sendJobApplication(Request $request){
        try{

              $request->validate([
                'name' => 'required',
                'email' => 'required|email',
                'phoneNumber' => 'required',
                'jobTitle' => 'required',
                'applicantMessage' => 'required',
                'resume' => 'required|file|mimes:pdf|max:5120',
            ]);

            $tempPath = $request->file('resume')->store('temp');

            SendJobApplication::dispatch(
                $request->name,
                $request->email,
                $request->phoneNumber,
                $request->jobTitle,
                $request->applicantMessage,
                $tempPath
            );

            return response()->json(['message' => 'Job application sent successfully']);


        } catch(Exception $e){
            return response()->json(['message' => $e->getMessage()]);
        }
      
    }
}
