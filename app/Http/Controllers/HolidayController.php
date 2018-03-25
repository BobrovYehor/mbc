<?php

namespace App\Http\Controllers;

use App\Http\Requests\DayRequest;
use App\Services\DayService;

class HolidayController extends Controller
{
    /**
     * @var DayService
     */
    protected $dayService;

    /**
     * HolidayController constructor.
     * @param DayService $dayService
     */
    public function __construct(DayService $dayService)
    {
        $this->dayService = $dayService;

    }

    /**
     * @param DayRequest $request
     * @return $this
     */
    public function check(DayRequest $request)
    {
        $date = strtotime($request->input('date'));

        return back()->with('message', $this->dayService->checkDate($date))
            ->withInput();
    }
}
