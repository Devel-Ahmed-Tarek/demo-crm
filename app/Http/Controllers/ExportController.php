<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Customer;
use App\Models\Unit;
use App\Models\Appointment;
use App\Models\User;
use App\Http\Controllers\Concerns\HandlesTeamVisibility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    use HandlesTeamVisibility;
    public function leads(Request $request)
    {
        $user = Auth::user();
        $query = Lead::with(['assignedUser', 'customer', 'team']);

        // Apply team visibility scope
        if ($user->isSalesAgent()) {
            $query->where('assigned_to', $user->id);
        } elseif ($user->isSalesSupervisor()) {
            $teamIds = $user->leaderTeamIds();
            if (!empty($teamIds)) {
                $query->whereIn('team_id', $teamIds);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        // Apply filters
        if ($request->has('source') && $request->source) {
            $query->where('source', $request->source);
        }
        if ($request->has('stage') && $request->stage) {
            $query->where('stage', $request->stage);
        }
        if ($request->has('assigned_to') && $request->assigned_to) {
            $query->where('assigned_to', $request->assigned_to);
        }
        if ($request->filled('team_id')) {
            $query->where('team_id', $request->team_id);
        }
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $leads = $query->latest()->get();

        return $this->exportToExcel(
            $leads,
            'Leads',
            [
                'ID' => 'id',
                __('Name') => 'name',
                __('Email') => 'email',
                __('Phone') => 'phone',
                __('Source') => 'source',
                __('Stage') => 'stage',
                __('Assigned To') => function($lead) {
                    return $lead->assignedUser->name ?? '-';
                },
                __('Team') => function($lead) {
                    return $lead->team->name ?? '-';
                },
                __('Created At') => function($lead) {
                    return $lead->created_at->format('Y-m-d H:i');
                },
            ]
        );
    }

    public function customers(Request $request)
    {
        $user = Auth::user();
        $query = Customer::with(['assignedUser', 'team']);

        // Apply team visibility scope
        if ($user->isSalesAgent()) {
            $query->where('assigned_to', $user->id);
        } elseif ($user->isSalesSupervisor()) {
            $teamIds = $user->leaderTeamIds();
            if (!empty($teamIds)) {
                $query->whereIn('team_id', $teamIds);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        // Apply filters
        if ($request->has('assigned_to') && $request->assigned_to) {
            $query->where('assigned_to', $request->assigned_to);
        }
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->latest()->get();

        return $this->exportToExcel(
            $customers,
            'Customers',
            [
                'ID' => 'id',
                __('Name') => 'name',
                __('Email') => 'email',
                __('Phone') => 'phone',
                __('Address') => 'address',
                __('Assigned To') => function($customer) {
                    return $customer->assignedUser->name ?? '-';
                },
                __('Team') => function($customer) {
                    return $customer->team->name ?? '-';
                },
                __('Created At') => function($customer) {
                    return $customer->created_at->format('Y-m-d H:i');
                },
            ]
        );
    }

    public function units(Request $request)
    {
        $user = Auth::user();
        $query = Unit::with(['reservedBy', 'soldTo']);

        // Apply team visibility scope for sold units
        if ($user->isSalesSupervisor()) {
            $teamIds = $user->leaderTeamIds();
            if (!empty($teamIds)) {
                $query->where(function($q) use ($teamIds) {
                    $q->where('status', '!=', 'sold')
                        ->orWhereHas('soldTo', function($customerQuery) use ($teamIds) {
                            $customerQuery->whereIn('team_id', $teamIds);
                        });
                });
            } else {
                $query->where('status', '!=', 'sold');
            }
        }

        // Apply filters
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        if ($request->has('location') && $request->location) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        $units = $query->latest()->get();

        return $this->exportToExcel(
            $units,
            'Units',
            [
                'ID' => 'id',
                __('Code') => 'code',
                __('Location') => 'location',
                __('Area') => 'area',
                __('Rooms') => 'rooms',
                __('Price') => 'price',
                __('Status') => 'status',
                __('Reserved By') => function($unit) {
                    return $unit->reservedBy->name ?? '-';
                },
                __('Sold To') => function($unit) {
                    return $unit->soldTo->name ?? '-';
                },
                __('Sales Comment') => 'sales_comment',
                __('Sold At') => function($unit) {
                    return $unit->sold_at ? $unit->sold_at->format('Y-m-d H:i') : '-';
                },
                __('Created At') => function($unit) {
                    return $unit->created_at->format('Y-m-d H:i');
                },
            ]
        );
    }

    public function appointments(Request $request)
    {
        $user = Auth::user();
        $query = Appointment::with(['customer', 'unit', 'user']);

        // Apply team visibility scope
        if ($user->isSalesAgent()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isSalesSupervisor()) {
            $teamIds = $user->leaderTeamIds();
            if (!empty($teamIds)) {
                $query->whereHas('customer', function ($q) use ($teamIds) {
                    $q->whereIn('team_id', $teamIds);
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        // Apply filters
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        if ($request->has('customer_id') && $request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }

        $appointments = $query->latest('appointment_date')->get();

        return $this->exportToExcel(
            $appointments,
            'Appointments',
            [
                'ID' => 'id',
                __('Customer') => function($appointment) {
                    return $appointment->customer->name ?? '-';
                },
                __('Unit') => function($appointment) {
                    return $appointment->unit->code ?? '-';
                },
                __('Date & Time') => function($appointment) {
                    return $appointment->appointment_date->format('Y-m-d H:i');
                },
                __('Price') => 'price',
                __('Status') => 'status',
                __('Created By') => function($appointment) {
                    return $appointment->user->name ?? '-';
                },
                __('Notes') => 'notes',
                __('Created At') => function($appointment) {
                    return $appointment->created_at->format('Y-m-d H:i');
                },
            ]
        );
    }

    protected function exportToExcel($data, $sheetName, $columns)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($sheetName);

        // Set headers
        $headers = array_keys($columns);
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '3B82F6']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
            $col++;
        }

        // Set data
        $row = 2;
        foreach ($data as $item) {
            $col = 'A';
            foreach ($columns as $column => $field) {
                if (is_callable($field)) {
                    $value = $field($item);
                } else {
                    $value = $item->$field ?? '-';
                }
                $sheet->setCellValue($col . $row, $value);
                $col++;
            }
            $row++;
        }

        // Auto-size columns
        foreach (range('A', $col) as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Add borders
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $sheet->getStyle('A1:' . $highestColumn . $highestRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);

        $filename = $sheetName . '_' . date('Y-m-d_His') . '.xlsx';

        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    public function reports(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $spreadsheet = new Spreadsheet();
        
        // Sheet 1: Lead Status Report
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Lead Status Report');
        
        $leadBaseQuery = $this->applyTeamScope(Lead::query(), $user);
        $leadStatusReport = (clone $leadBaseQuery)
            ->select('stage', DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('stage')
            ->get()
            ->pluck('count', 'stage');

        $sheet1->setCellValue('A1', __('Lead Status Report'));
        $sheet1->setCellValue('A2', __('Period') . ': ' . $startDate . ' - ' . $endDate);
        $sheet1->setCellValue('A4', __('Status'));
        $sheet1->setCellValue('B4', __('Count'));

        $row = 5;
        foreach ($leadStatusReport as $stage => $count) {
            $sheet1->setCellValue('A' . $row, __(ucfirst($stage)));
            $sheet1->setCellValue('B' . $row, $count);
            $row++;
        }

        // Sheet 2: Sales Performance
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Sales Performance');

        $salesPerformanceQuery = User::whereIn('role', ['sales_supervisor', 'sales_agent']);

        if ($user->isSalesAgent()) {
            $salesPerformanceQuery->where('id', $user->id);
        } elseif ($user->isSalesSupervisor()) {
            $teamIds = $user->leaderTeamIds();
            if (empty($teamIds)) {
                $salesPerformanceQuery->whereRaw('1 = 0');
            } else {
                $salesPerformanceQuery->whereHas('teams', function ($teamQuery) use ($teamIds) {
                    $teamQuery->whereIn('teams.id', $teamIds);
                });
            }
        }

        $salesPerformance = $salesPerformanceQuery
            ->withCount([
                'assignedLeads as total_leads' => function($query) use ($startDate, $endDate, $user) {
                    $this->applyTeamScope($query, $user)->whereBetween('created_at', [$startDate, $endDate]);
                },
                'assignedLeads as won_leads' => function($query) use ($startDate, $endDate, $user) {
                    $this->applyTeamScope($query, $user)->where('stage', 'won')->whereBetween('created_at', [$startDate, $endDate]);
                },
            ])
            ->get()
            ->map(function($user) {
                $user->conversion_rate = $user->total_leads > 0 ? ($user->won_leads / $user->total_leads) * 100 : 0;
                return $user;
            });

        $sheet2->setCellValue('A1', __('Sales Performance Report'));
        $sheet2->setCellValue('A2', __('Period') . ': ' . $startDate . ' - ' . $endDate);
        $sheet2->setCellValue('A4', __('User'));
        $sheet2->setCellValue('B4', __('Total Leads'));
        $sheet2->setCellValue('C4', __('Won Leads'));
        $sheet2->setCellValue('D4', __('Conversion Rate'));

        $row = 5;
        foreach ($salesPerformance as $perfUser) {
            $sheet2->setCellValue('A' . $row, $perfUser->name);
            $sheet2->setCellValue('B' . $row, $perfUser->total_leads);
            $sheet2->setCellValue('C' . $row, $perfUser->won_leads);
            $sheet2->setCellValue('D' . $row, number_format($perfUser->conversion_rate, 2) . '%');
            $row++;
        }

        // Sheet 3: Conversion Summary
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Conversion Summary');

        $totalLeads = (clone $leadBaseQuery)->whereBetween('created_at', [$startDate, $endDate])->count();
        $wonLeads = (clone $leadBaseQuery)->where('stage', 'won')->whereBetween('created_at', [$startDate, $endDate])->count();
        $lostLeads = (clone $leadBaseQuery)->where('stage', 'lost')->whereBetween('created_at', [$startDate, $endDate])->count();
        $conversionRate = ($wonLeads + $lostLeads) > 0 ? ($wonLeads / ($wonLeads + $lostLeads)) * 100 : 0;
        $deadLeads = (clone $leadBaseQuery)
            ->whereIn('stage', ['new', 'contacted', 'follow-up'])
            ->where(function($query) {
                $query->whereNull('last_contacted_at')
                    ->orWhere('last_contacted_at', '<', now()->subDays(30));
            })
            ->count();

        $sheet3->setCellValue('A1', __('Conversion Summary'));
        $sheet3->setCellValue('A2', __('Period') . ': ' . $startDate . ' - ' . $endDate);
        $sheet3->setCellValue('A4', __('Metric'));
        $sheet3->setCellValue('B4', __('Value'));

        $metrics = [
            __('Total Leads') => $totalLeads,
            __('Won Leads') => $wonLeads,
            __('Lost Leads') => $lostLeads,
            __('Conversion Rate') => number_format($conversionRate, 2) . '%',
            __('Dead Leads') => $deadLeads,
        ];

        $row = 5;
        foreach ($metrics as $label => $value) {
            $sheet3->setCellValue('A' . $row, $label);
            $sheet3->setCellValue('B' . $row, $value);
            $row++;
        }

        // Apply styling to all sheets
        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            
            // Style headers
            $sheet->getStyle('A4:' . $highestColumn . '4')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '3B82F6']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            // Auto-size columns
            foreach (range('A', $highestColumn) as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            // Add borders
            if ($highestRow > 4) {
                $sheet->getStyle('A4:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC']
                        ]
                    ]
                ]);
            }
        }

        // Set first sheet as active
        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'Reports_' . date('Y-m-d_His') . '.xlsx';

        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    public function userData(Request $request, User $user)
    {
        $this->checkAuth();
        
        // Validate date range
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $spreadsheet = new Spreadsheet();
        
        // Sheet 1: User Leads
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle(__('Leads'));
        
        $leads = $user->assignedLeads()
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with(['customer', 'team'])
            ->latest()
            ->get();

        $sheet1->setCellValue('A1', __('User Leads Report'));
        $sheet1->setCellValue('A2', __('User') . ': ' . $user->name);
        $sheet1->setCellValue('A3', __('Period') . ': ' . $startDate . ' - ' . $endDate);
        
        $headers = [
            __('ID'),
            __('Name'),
            __('Email'),
            __('Phone'),
            __('Source'),
            __('Stage'),
            __('Customer'),
            __('Team'),
            __('Created At'),
            __('Last Contacted'),
        ];
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet1->setCellValue($col . '5', $header);
            $col++;
        }

        $row = 6;
        foreach ($leads as $lead) {
            $sheet1->setCellValue('A' . $row, $lead->id);
            $sheet1->setCellValue('B' . $row, $lead->name);
            $sheet1->setCellValue('C' . $row, $lead->email ?? '-');
            $sheet1->setCellValue('D' . $row, $lead->phone ?? '-');
            $sheet1->setCellValue('E' . $row, __(ucfirst($lead->source ?? '-')));
            $sheet1->setCellValue('F' . $row, __(ucfirst($lead->stage ?? '-')));
            $sheet1->setCellValue('G' . $row, $lead->customer->name ?? '-');
            $sheet1->setCellValue('H' . $row, $lead->team->name ?? '-');
            $sheet1->setCellValue('I' . $row, $lead->created_at->format('Y-m-d H:i'));
            $sheet1->setCellValue('J' . $row, $lead->last_contacted_at ? $lead->last_contacted_at->format('Y-m-d H:i') : '-');
            $row++;
        }

        // Sheet 2: User Customers
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle(__('Customers'));

        $customers = $user->assignedCustomers()
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with(['team'])
            ->latest()
            ->get();

        $sheet2->setCellValue('A1', __('User Customers Report'));
        $sheet2->setCellValue('A2', __('User') . ': ' . $user->name);
        $sheet2->setCellValue('A3', __('Period') . ': ' . $startDate . ' - ' . $endDate);
        
        $headers = [
            __('ID'),
            __('Name'),
            __('Email'),
            __('Phone'),
            __('Address'),
            __('Team'),
            __('Created At'),
        ];
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet2->setCellValue($col . '5', $header);
            $col++;
        }

        $row = 6;
        foreach ($customers as $customer) {
            $sheet2->setCellValue('A' . $row, $customer->id);
            $sheet2->setCellValue('B' . $row, $customer->name);
            $sheet2->setCellValue('C' . $row, $customer->email ?? '-');
            $sheet2->setCellValue('D' . $row, $customer->phone ?? '-');
            $sheet2->setCellValue('E' . $row, $customer->address ?? '-');
            $sheet2->setCellValue('F' . $row, $customer->team->name ?? '-');
            $sheet2->setCellValue('G' . $row, $customer->created_at->format('Y-m-d H:i'));
            $row++;
        }

        // Sheet 3: User Appointments
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle(__('Appointments'));

        $appointments = $user->appointments()
            ->whereBetween('appointment_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->with(['customer', 'unit'])
            ->latest('appointment_date')
            ->get();

        $sheet3->setCellValue('A1', __('User Appointments Report'));
        $sheet3->setCellValue('A2', __('User') . ': ' . $user->name);
        $sheet3->setCellValue('A3', __('Period') . ': ' . $startDate . ' - ' . $endDate);
        
        $headers = [
            __('ID'),
            __('Customer'),
            __('Unit'),
            __('Date & Time'),
            __('Price'),
            __('Status'),
            __('Notes'),
            __('Created At'),
        ];
        
        $col = 'A';
        foreach ($headers as $header) {
            $sheet3->setCellValue($col . '5', $header);
            $col++;
        }

        $row = 6;
        foreach ($appointments as $appointment) {
            $sheet3->setCellValue('A' . $row, $appointment->id);
            $sheet3->setCellValue('B' . $row, $appointment->customer->name ?? '-');
            $sheet3->setCellValue('C' . $row, $appointment->unit->code ?? '-');
            $sheet3->setCellValue('D' . $row, $appointment->appointment_date->format('Y-m-d H:i'));
            $sheet3->setCellValue('E' . $row, $appointment->price ?? '-');
            $sheet3->setCellValue('F' . $row, __(ucfirst($appointment->status ?? '-')));
            $sheet3->setCellValue('G' . $row, $appointment->notes ?? '-');
            $sheet3->setCellValue('H' . $row, $appointment->created_at->format('Y-m-d H:i'));
            $row++;
        }

        // Sheet 4: Statistics Summary
        $sheet4 = $spreadsheet->createSheet();
        $sheet4->setTitle(__('Statistics'));

        $totalLeads = $user->assignedLeads()
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->count();
        $wonLeads = $user->assignedLeads()
            ->where('stage', 'won')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->count();
        $activeLeads = $user->assignedLeads()
            ->whereIn('stage', ['new', 'contacted', 'follow-up', 'proposal'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->count();
        $totalCustomers = $user->assignedCustomers()
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->count();
        $totalAppointments = $user->appointments()
            ->whereBetween('appointment_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->count();
        $conversionRate = $totalLeads > 0 ? ($wonLeads / $totalLeads) * 100 : 0;

        $sheet4->setCellValue('A1', __('User Statistics Summary'));
        $sheet4->setCellValue('A2', __('User') . ': ' . $user->name);
        $sheet4->setCellValue('A3', __('Period') . ': ' . $startDate . ' - ' . $endDate);
        
        $sheet4->setCellValue('A5', __('Metric'));
        $sheet4->setCellValue('B5', __('Value'));

        $metrics = [
            __('Total Leads') => $totalLeads,
            __('Won Leads') => $wonLeads,
            __('Active Leads') => $activeLeads,
            __('Total Customers') => $totalCustomers,
            __('Total Appointments') => $totalAppointments,
            __('Conversion Rate') => number_format($conversionRate, 2) . '%',
        ];

        $row = 6;
        foreach ($metrics as $label => $value) {
            $sheet4->setCellValue('A' . $row, $label);
            $sheet4->setCellValue('B' . $row, $value);
            $row++;
        }

        // Apply styling to all sheets
        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            
            // Style headers (row 5)
            if ($highestRow >= 5) {
                $sheet->getStyle('A5:' . $highestColumn . '5')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '3B82F6']
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            }

            // Auto-size columns
            foreach (range('A', $highestColumn) as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            // Add borders
            if ($highestRow > 5) {
                $sheet->getStyle('A5:' . $highestColumn . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC']
                        ]
                    ]
                ]);
            }
        }

        // Set first sheet as active
        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'User_Report_' . str_replace(' ', '_', $user->name) . '_' . date('Y-m-d_His') . '.xlsx';

        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $filename . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    private function checkAuth()
    {
        if (!Auth::check()) {
            abort(401, 'Unauthenticated');
        }
    }
}

