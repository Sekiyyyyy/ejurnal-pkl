<?php

namespace App\Services;

use App\Models\WeeklyApproval;
use App\Models\Journal;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;

class ValidationService
{
    public function getValidations($majorId = null, $type = null, $status = null, $search = null, $perPage = 12)
    {
        // 1. Query Weekly Approvals
        $waQuery = WeeklyApproval::with(['journal.student.major'])
            ->where(function($q) {
                $q->whereNotNull('instructor_live_photo')
                  ->orWhereNotNull('instructor_paraf')
                  ->orWhere('is_rejected', true);
            });

        if ($majorId) {
            $waQuery->whereHas('journal.student', function($q) use ($majorId) {
                $q->where('major_id', $majorId);
            });
        }
        if ($search) {
            $waQuery->whereHas('journal.student', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        // 2. Query Monitoring Guru
        $monitoringQuery = Journal::with(['student.major'])
            ->where(function($q) {
                $q->whereNotNull('teacher_live_photo')
                  ->orWhereNotNull('teacher_signature')
                  ->orWhereNotNull('teacher_rejection_note');
            });

        if ($majorId) {
            $monitoringQuery->whereHas('student', function($q) use ($majorId) {
                $q->where('major_id', $majorId);
            });
        }
        if ($search) {
            $monitoringQuery->whereHas('student', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        // 3. Query Final Assessment
        $finalQuery = Journal::with(['student.major'])
            ->where(function($q) {
                $q->whereNotNull('instructor_live_photo')
                  ->orWhereNotNull('instructor_signature')
                  ->orWhereNotNull('instructor_rejection_note');
            });

        if ($majorId) {
            $finalQuery->whereHas('student', function($q) use ($majorId) {
                $q->where('major_id', $majorId);
            });
        }
        if ($search) {
            $finalQuery->whereHas('student', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $items = collect();

        // Process Weekly Approvals
        if (!$type || $type === 'all' || $type === 'weekly') {
            foreach ($waQuery->get() as $wa) {
                if (!$wa->journal || !$wa->journal->student) continue;
                $isRejected = (bool)$wa->is_rejected;
                $isApproved = !empty($wa->approved_at);
                $itemStatus = $isRejected ? 'rejected' : ($isApproved ? 'approved' : 'pending');

                if ($status && $status !== 'all' && $status !== $itemStatus) continue;

                $items->push([
                    'id' => $wa->id,
                    'journal_id' => $wa->journal_id,
                    'type' => 'weekly',
                    'type_badge' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                    'type_icon' => 'fa-calendar-check',
                    'type_label' => 'ACC Kegiatan Mingguan',
                    'title' => 'Persetujuan Mingguan - Minggu ' . $wa->week_number,
                    'sub_title' => 'Fase ' . $wa->journal->phase . ' • ' . ($wa->journal->company_name ?? 'Perusahaan'),
                    'student' => $wa->journal->student,
                    'photo' => $wa->instructor_live_photo,
                    'signature' => $wa->instructor_paraf,
                    'person_name' => $wa->journal->instructor_name ?? 'Instruktur DU/DI',
                    'person_role' => 'Instruktur DU/DI',
                    'status' => $itemStatus,
                    'status_label' => $isRejected ? 'Ditolak' : ($isApproved ? 'Disetujui' : 'Menunggu Review'),
                    'status_badge' => $isRejected ? 'bg-red-50 text-red-700 border-red-200' : ($isApproved ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200'),
                    'rejection_note' => $wa->rejection_note,
                    'date' => $wa->approved_at ?? $wa->created_at,
                    'date_formatted' => Carbon::parse($wa->approved_at ?? $wa->created_at)->translatedFormat('d M Y, H:i'),
                    'reject_action_admin' => route('admin.students.reject-weekly-approval', $wa->id),
                    'reject_action_kaprodi' => route('kaprodi.journal.reject-weekly', $wa->id),
                    'delete_action_admin' => route('admin.validations.destroy', ['type' => 'weekly', 'id' => $wa->id]),
                    'delete_action_kaprodi' => route('kaprodi.validations.destroy', ['type' => 'weekly', 'id' => $wa->id]),
                    'journal_show_admin' => route('admin.students.show', $wa->journal->student_id),
                    'journal_show_kaprodi' => route('kaprodi.journal.show', $wa->journal_id),
                ]);
            }
        }

        // Process Monitoring
        if (!$type || $type === 'all' || $type === 'monitoring') {
            foreach ($monitoringQuery->get() as $j) {
                if (!$j->student) continue;
                $isRejected = !empty($j->teacher_rejection_note);
                $isApproved = !empty($j->monitoring_locked_at);
                $itemStatus = $isRejected ? 'rejected' : ($isApproved ? 'approved' : 'pending');

                if ($status && $status !== 'all' && $status !== $itemStatus) continue;

                $items->push([
                    'id' => $j->id,
                    'journal_id' => $j->id,
                    'type' => 'monitoring',
                    'type_badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'type_icon' => 'fa-clipboard-check',
                    'type_label' => 'Monitoring Guru',
                    'title' => 'Catatan Observasi Guru Pembimbing',
                    'sub_title' => 'Fase ' . $j->phase . ' • ' . ($j->company_name ?? 'Perusahaan'),
                    'student' => $j->student,
                    'photo' => $j->teacher_live_photo,
                    'signature' => $j->teacher_signature,
                    'person_name' => $j->teacher_name ?? 'Guru Pembimbing',
                    'person_role' => 'Guru Pembimbing Sekolah',
                    'status' => $itemStatus,
                    'status_label' => $isRejected ? 'Ditolak' : ($isApproved ? 'Selesai / Terkunci' : 'Menunggu Review'),
                    'status_badge' => $isRejected ? 'bg-red-50 text-red-700 border-red-200' : ($isApproved ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200'),
                    'rejection_note' => $j->teacher_rejection_note,
                    'date' => $j->monitoring_locked_at ?? $j->updated_at,
                    'date_formatted' => Carbon::parse($j->monitoring_locked_at ?? $j->updated_at)->translatedFormat('d M Y, H:i'),
                    'reject_action_admin' => route('admin.students.reject-monitoring', $j->id),
                    'reject_action_kaprodi' => route('kaprodi.journal.reject-monitoring', $j->id),
                    'delete_action_admin' => route('admin.validations.destroy', ['type' => 'monitoring', 'id' => $j->id]),
                    'delete_action_kaprodi' => route('kaprodi.validations.destroy', ['type' => 'monitoring', 'id' => $j->id]),
                    'journal_show_admin' => route('admin.students.show', $j->student_id),
                    'journal_show_kaprodi' => route('kaprodi.journal.show', $j->id),
                ]);
            }
        }

        // Process Final Assessment
        if (!$type || $type === 'all' || $type === 'final') {
            foreach ($finalQuery->get() as $j) {
                if (!$j->student) continue;
                $isRejected = !empty($j->instructor_rejection_note);
                $isApproved = ($j->status === 'COMPLETED');
                $itemStatus = $isRejected ? 'rejected' : ($isApproved ? 'approved' : 'pending');

                if ($status && $status !== 'all' && $status !== $itemStatus) continue;

                $items->push([
                    'id' => $j->id,
                    'journal_id' => $j->id,
                    'type' => 'final',
                    'type_badge' => 'bg-purple-50 text-purple-700 border-purple-200',
                    'type_icon' => 'fa-award',
                    'type_label' => 'Penilaian Akhir PKL',
                    'title' => 'Pengesahan Nilai Akhir Instruktur',
                    'sub_title' => 'Fase ' . $j->phase . ' • ' . ($j->company_name ?? 'Perusahaan'),
                    'student' => $j->student,
                    'photo' => $j->instructor_live_photo,
                    'signature' => $j->instructor_signature,
                    'person_name' => $j->instructor_name ?? 'Instruktur DU/DI',
                    'person_role' => 'Instruktur DU/DI',
                    'status' => $itemStatus,
                    'status_label' => $isRejected ? 'Ditolak' : ($isApproved ? 'Selesai / Sah' : 'Menunggu Review'),
                    'status_badge' => $isRejected ? 'bg-red-50 text-red-700 border-red-200' : ($isApproved ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-700 border-amber-200'),
                    'rejection_note' => $j->instructor_rejection_note,
                    'date' => $j->updated_at,
                    'date_formatted' => Carbon::parse($j->updated_at)->translatedFormat('d M Y, H:i'),
                    'reject_action_admin' => route('admin.students.reject-final-assessment', $j->id),
                    'reject_action_kaprodi' => route('kaprodi.journal.reject-final', $j->id),
                    'delete_action_admin' => route('admin.validations.destroy', ['type' => 'final', 'id' => $j->id]),
                    'delete_action_kaprodi' => route('kaprodi.validations.destroy', ['type' => 'final', 'id' => $j->id]),
                    'journal_show_admin' => route('admin.students.show', $j->student_id),
                    'journal_show_kaprodi' => route('kaprodi.journal.show', $j->id),
                ]);
            }
        }

        // Stats before status filter if needed, or from items
        $totalCount = $items->count();
        $pendingCount = $items->where('status', 'pending')->count();
        $rejectedCount = $items->where('status', 'rejected')->count();
        $approvedCount = $items->where('status', 'approved')->count();

        // Sort by date descending
        $sorted = $items->sortByDesc('date')->values();

        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $currentItems = $sorted->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginated = new LengthAwarePaginator(
            $currentItems,
            $sorted->count(),
            $perPage,
            $currentPage,
            ['path' => Paginator::resolveCurrentPath(), 'query' => request()->query()]
        );

        return [
            'paginated' => $paginated,
            'stats' => [
                'total' => $totalCount,
                'pending' => $pendingCount,
                'rejected' => $rejectedCount,
                'approved' => $approvedCount,
            ]
        ];
    }

    public function deleteValidation($type, $id, $majorId = null)
    {
        if ($type === 'weekly') {
            $query = WeeklyApproval::query();
            if ($majorId) {
                $query->whereHas('journal.student', function ($q) use ($majorId) {
                    $q->where('major_id', $majorId);
                });
            }
            $wa = $query->findOrFail($id);

            // 1. Hapus file fisik live photo dan paraf dari disk public
            if ($wa->instructor_live_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($wa->instructor_live_photo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($wa->instructor_live_photo);
            }
            if ($wa->instructor_paraf && \Illuminate\Support\Facades\Storage::disk('public')->exists($wa->instructor_paraf)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($wa->instructor_paraf);
            }

            // 2. Un-approve kegiatan harian minggu tersebut
            $journal = $wa->journal;
            if ($journal) {
                $startDate = $journal->start_date ? Carbon::parse($journal->start_date)->startOfWeek() : now()->startOfWeek();
                $activities = $journal->dailyActivities()->where('is_approved', true)->get()->filter(function($activity) use ($wa, $startDate) {
                    $activityDate = Carbon::parse($activity->date)->startOfWeek();
                    $relativeWeek = $startDate->diffInWeeks($activityDate) + 1;
                    return $relativeWeek == $wa->week_number;
                });

                foreach($activities as $activity) {
                    $activity->update(['is_approved' => false]);
                }

                if ($journal->status === 'COMPLETED') {
                    $journal->status = 'IN_PROGRESS';
                    $student = $journal->student;
                    if ($student) {
                        if ($journal->phase == 1) {
                            $student->update(['jurnal_1_completed_at' => null]);
                        } else if ($journal->phase == 2) {
                            $student->update(['jurnal_2_completed_at' => null]);
                        }
                    }
                    $journal->save();
                }
            }

            // 3. Hapus record weekly approval
            $wa->delete();

            return 'Bukti ACC Mingguan berhasil dihapus secara permanen.';
        }

        if ($type === 'monitoring') {
            $query = Journal::query();
            if ($majorId) {
                $query->whereHas('student', function ($q) use ($majorId) {
                    $q->where('major_id', $majorId);
                });
            }
            $journal = $query->findOrFail($id);

            // 1. Hapus file foto live & tanda tangan guru
            if ($journal->teacher_live_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($journal->teacher_live_photo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($journal->teacher_live_photo);
            }
            if ($journal->teacher_signature && \Illuminate\Support\Facades\Storage::disk('public')->exists($journal->teacher_signature)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($journal->teacher_signature);
            }

            // 2. Kosongkan bukti & buka lock monitoring
            $journal->teacher_live_photo = null;
            $journal->teacher_signature = null;
            $journal->monitoring_locked_at = null;
            $journal->teacher_rejection_note = null;

            if ($journal->status === 'COMPLETED') {
                $journal->status = 'IN_PROGRESS';
                $student = $journal->student;
                if ($student) {
                    if ($journal->phase == 1) {
                        $student->update(['jurnal_1_completed_at' => null]);
                    } else if ($journal->phase == 2) {
                        $student->update(['jurnal_2_completed_at' => null]);
                    }
                }
            }
            if ($journal->kaprodi_status === 'APPROVED') {
                $journal->kaprodi_status = 'PENDING';
            }
            $journal->save();

            return 'Bukti foto live & tanda tangan Monitoring Guru berhasil dihapus.';
        }

        if ($type === 'final') {
            $query = Journal::query();
            if ($majorId) {
                $query->whereHas('student', function ($q) use ($majorId) {
                    $q->where('major_id', $majorId);
                });
            }
            $journal = $query->findOrFail($id);

            // 1. Hapus file foto live & tanda tangan instruktur
            if ($journal->instructor_live_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($journal->instructor_live_photo)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($journal->instructor_live_photo);
            }
            if ($journal->instructor_signature && \Illuminate\Support\Facades\Storage::disk('public')->exists($journal->instructor_signature)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($journal->instructor_signature);
            }

            // 2. Kosongkan bukti pengesahan nilai akhir
            $journal->instructor_live_photo = null;
            $journal->instructor_signature = null;
            $journal->instructor_rejection_note = null;

            if ($journal->status === 'COMPLETED') {
                $journal->status = 'IN_PROGRESS';
                $student = $journal->student;
                if ($student) {
                    if ($journal->phase == 1) {
                        $student->update(['jurnal_1_completed_at' => null]);
                    } else if ($journal->phase == 2) {
                        $student->update(['jurnal_2_completed_at' => null]);
                    }
                }
            }
            if ($journal->kaprodi_status === 'APPROVED') {
                $journal->kaprodi_status = 'PENDING';
            }
            $journal->save();

            return 'Bukti foto live & tanda tangan Penilaian Akhir berhasil dihapus.';
        }

        throw new \InvalidArgumentException('Tipe bukti tidak valid.');
    }
}
