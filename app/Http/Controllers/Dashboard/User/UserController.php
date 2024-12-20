<?php

namespace App\Http\Controllers\Dashboard\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Users\CreateUserRequest;
use App\Http\Requests\Dashboard\Users\UpdateUserRequest;
use App\Models\Booking;
use App\Models\TimeSlot;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Gate::denies("access-dashboard")) {
                return abort(403, "Unauthorized");
            }
            return $next($request);
        });
    }

    public function index()
    {
        $title = "Delete User!";
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);

        return view("dashboard.users.index", [
            "title_page" => "Ohana Pilates | Users"
        ]);
    }

    /* public function getData()
    {
        $query = User::with("profile", "roles");

        if (auth()->user()->hasRole('admin')) {
            // Jika yang login adalah admin, tampilkan user dengan role admin, coach, dan client saja
            $query->whereHas("roles", function ($query) {
                $query->whereIn("name", ["admin", "coach", "client"]);
            });
        } elseif (auth()->user()->hasRole('super_admin')) {
            // Jika yang login adalah super_admin, tampilkan semua user kecuali satu user tertentu (berdasarkan email)
            $query->where("email", "!=", "support@ptmfs.co.id");
        }

        $users = $query->get();

        return DataTables::of($users)
        ->addColumn("phone", function ($user) {
            return $user->profile->phone ?? "N/A";
        })
        ->addColumn("gender", function ($user) {
            return ucfirst($user->profile->gender) ?? "N/A";
        })
        ->addColumn("platform", function ($user) {
            $socialAccount = $user->socialAccounts->first();
            return $socialAccount ? ucfirst($socialAccount->provider) : "-";
        })
        ->addColumn("role", function ($user) {
            return ucfirst($user->roles->pluck("name")->first() ?? "N/A");
        })
        ->addColumn("action", function ($user) {
            $btn = '<div class="btn-group mr-1">';

            // Tombol Edit dan View tetap muncul
            $btn .= '<a href="' . route("users.edit", ["user" => $user->id]) . '" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-fw fa-edit"></i></a> ';
            $btn .= '<a href="' . route("users.view", ["user" => $user->id]) . '" class="btn btn-info btn-sm" title="View"><i class="fas fa-fw fa-eye"></i></a> ';

            // Menambahkan tombol Delete berdasarkan role pengguna yang login
            if (auth()->user()->hasRole('super_admin')) {
                // Super Admin tidak bisa menghapus dirinya sendiri dan sesama super_admin, tapi bisa menghapus admin
                if (auth()->user()->id !== $user->id && !$user->hasRole('super_admin')) {
                    $btn .= '<a href="' . route("users.delete", ["user" => $user->id]) . '" class="btn btn-danger btn-sm" title="Delete" data-confirm-delete="true"><i class="fas fa-fw fa-trash"></i></a>';
                }
            } elseif (auth()->user()->hasRole('admin')) {
                // Admin tidak bisa menghapus user dengan role admin dan super_admin
                if (!$user->hasRole('admin') && !$user->hasRole('super_admin')) {
                    $btn .= '<a href="' . route("users.delete", ["user" => $user->id]) . '" class="btn btn-danger btn-sm" title="Delete" data-confirm-delete="true"><i class="fas fa-fw fa-trash"></i></a>';
                }
            }

            $btn .= '</div>';
            return $btn;
        })
        ->make(true);
    } */

    public function getData()
    {
        $query = User::select(
            'users.id',
            'users.name',
            'users.email',
            'users.credit_balance',
            'user_profiles.phone',
            'user_profiles.gender',
            'users.created_at as user_created_at',
            'social_accounts.provider',
            'roles.name as role_name'
        )
        ->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id')
        ->leftJoin('social_accounts', 'users.id', '=', 'social_accounts.user_id')
        ->leftJoin('user_roles', 'users.id', '=', 'user_roles.user_id')
        ->leftJoin('roles', 'user_roles.role_id', '=', 'roles.id');

        if (auth()->user()->hasRole('admin')) {
            // Jika yang login adalah admin, tampilkan user dengan role admin, coach, dan client saja
            $query->whereHas("roles", function ($query) {
                $query->whereIn("name", ["admin", "coach", "client"]);
            });
        } elseif (auth()->user()->hasRole('super_admin')) {
            // Jika yang login adalah super_admin, tampilkan semua user kecuali satu user tertentu (berdasarkan email)
            $query->where("email", "!=", "support@ptmfs.co.id");
        }

        return DataTables::of($query)
        ->addColumn("phone", function ($user) {
            return $user->profile->phone ?? "N/A";
        })
        ->addColumn("gender", function ($user) {
            return ucfirst($user->profile->gender) ?? "N/A";
        })
        ->addColumn("platform", function ($user) {
            $socialAccount = $user->socialAccounts->first();
            return $socialAccount ? ucfirst($socialAccount->provider) : "-";
        })
        ->addColumn("role", function ($user) {
            return ucfirst($user->roles->pluck("name")->first() ?? "N/A");
        })
        ->addColumn("action", function ($user) {
            $btn = '<div class="btn-group mr-1">';

            // Tombol Edit dan View tetap muncul
            $btn .= '<a href="' . route("users.edit", ["user" => $user->id]) . '" class="btn btn-warning btn-sm" title="Edit"><i class="fas fa-fw fa-edit"></i></a> ';
            $btn .= '<a href="' . route("users.view", ["user" => $user->id]) . '" class="btn btn-info btn-sm" title="View"><i class="fas fa-fw fa-eye"></i></a> ';

            // Menambahkan tombol Delete berdasarkan role pengguna yang login
            if (auth()->user()->hasRole('super_admin')) {
                // Super Admin tidak bisa menghapus dirinya sendiri dan sesama super_admin, tapi bisa menghapus admin
                if (auth()->user()->id !== $user->id && !$user->hasRole('super_admin')) {
                    $btn .= '<a href="' . route("users.delete", ["user" => $user->id]) . '" class="btn btn-danger btn-sm" title="Delete" data-confirm-delete="true"><i class="fas fa-fw fa-trash"></i></a>';
                }
            } elseif (auth()->user()->hasRole('admin')) {
                // Admin tidak bisa menghapus user dengan role admin dan super_admin
                if (!$user->hasRole('admin') && !$user->hasRole('super_admin')) {
                    $btn .= '<a href="' . route("users.delete", ["user" => $user->id]) . '" class="btn btn-danger btn-sm" title="Delete" data-confirm-delete="true"><i class="fas fa-fw fa-trash"></i></a>';
                }
            }

            $btn .= '</div>';
            return $btn;
        })
        ->make(true);
    }

    public function create()
    {
        $action = route("users.store");

        return view("dashboard.users.form.form", [
            "title_page" => "Ohana Pilates | Add New User",
            "action" => $action,
            "method" => "POST"
        ]);
    }

    public function store(CreateUserRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            $user = User::create([
                "name" => $validated["name"],
                "email" => $validated["email"],
                "password" => Hash::make($validated["password"]),
                "registration_type" => "form",
                "email_verified_at" => Carbon::now()
            ]);

            $userId = $user->id;

            // Menangani upload gambar
            if ($request->hasFile("profile_picture")) {
                $imageName = uniqid() . "." . $request->profile_picture->extension();
                // Simpan gambar di storage (folder storage/app/public/images/profile)
                $path = $request->file("profile_picture")->storeAs("images/profile", $imageName, "public");

                // Jika ingin menyimpan path untuk disimpan ke database
                $imageName = $path; // Simpan path yang bisa diakses via 'storage/images/profile/uniqueimagename.extension'
            } else {
                $imageName = null; // Atau set ke default image
            }

            UserProfile::create([
                "user_id" => $userId,                
                "gender" => $validated["gender"],
                "phone" => $validated["phone"],
                "address" => isset($validated["address"]) && !empty($validated["address"]) ? $validated["address"] : null,
                "profile_picture" => $imageName
            ]);

            $user->roles()->attach($validated["role"]);

            DB::commit();

            alert()->success("Yeay!", "Successfully added new user.");
            return redirect()->route("users.index");
        } catch (\Exception $e) {
            Log::error("Error adding user in UserController@store: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while adding a new user, please try again.");
            return redirect()->back();
        }
    }

    public function view(User $user)
    {
        // Eager load relasi profile dan roles, gunakan instance $user secara langsung
        $userData = $user->load(["profile", "roles"]);

        // Ambil role pertama (jika user memiliki lebih dari satu role, sesuaikan logikanya)
        $roleName = ucfirst($userData->roles->pluck("name")->first());

        $timeSlots = TimeSlot::get();

        // Kirim data ke view menggunakan compact
        return view("dashboard.users.profile.profile", compact("userData", "roleName", "timeSlots"))
            ->with("title_page", "Ohana Pilates | User Profile");
    }

    /* public function getDataBookings(User $user, Request $request)
    {
        // Query dasar
        $query = Booking::query()
            ->with(['lessonSchedule', 'lessonSchedule.timeSlot', 'user.profile'])
            ->where('user_id', $user->id);

        // Menambahkan pencarian berdasarkan parameter search
        if ($request->has('search') && $request->search['value']) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->whereHas('lessonSchedule', function ($q) use ($searchValue) {
                    $q->where('lesson_code', 'like', "%{$searchValue}%");
                })
                    ->orWhereHas('user.profile', function ($q) use ($searchValue) {
                        $q->where('name', 'like', "%{$searchValue}%");
                    })
                    ->orWhereHas('lessonSchedule.timeSlot', function ($q) use ($searchValue) {
                        $q->where('start_time', 'like', "%{$searchValue}%");
                    });
            });
        }

        // Filter berdasarkan tanggal jika ada
        if ($request->has("date") && $request->date) {
            $filterDate = Carbon::parse($request->date)->format('Y-m-d');
            $query->whereHas('lessonSchedule', function ($q) use ($filterDate) {
                $q->whereDate('date', '=', $filterDate);
            });
        }

        // Filter berdasarkan waktu jika ada
        if ($request->has("time_slot_id") && $request->time_slot_id) {
            $query->whereHas('lessonSchedule', function ($q) use ($request) {
                $q->where('time_slot_id', '=', $request->time_slot_id);
            });
        }

        return DataTables::of($query)
            ->addColumn("lesson_time", function ($booking) {
                $scheduleDate = Carbon::parse($booking->lessonSchedule->date)->format('d-m-Y');
                $scheduleTime = date("H:i", strtotime($booking->lessonSchedule->timeSlot->start_time));
                return "<strong>" . $scheduleDate . "</strong><br>" . $scheduleTime;
            })
            ->addColumn("lesson_code", function ($booking) {
                return $booking->lessonSchedule->lesson_code;
            })
            ->addColumn("booked_at", function ($booking) {
                $scheduleDate = Carbon::parse($booking->created_at);
                $formattedDate = $scheduleDate->format('d-m-Y');
                $formattedTime = $scheduleDate->format('H:i');
                return "<strong>" . $formattedDate . "</strong><br>" . $formattedTime;
            })
            ->rawColumns(["lesson_time", "booked_at"])
            ->make(true);
    } */

    public function getDataBookings(User $user, Request $request)
    {
        $today = Carbon::today()->format('Y-m-d');

        // Query dasar dengan eager loading
        $query = Booking::select('bookings.*')
        ->join('lesson_schedules', 'bookings.lesson_schedule_id', '=', 'lesson_schedules.id')
        ->join('time_slots', 'lesson_schedules.time_slot_id', '=', 'time_slots.id')
        ->join('users', 'bookings.user_id', '=', 'users.id')
        ->join('user_profiles', 'users.id', '=', 'user_profiles.user_id')
        ->where('bookings.user_id', $user->id)
        ->where('lesson_schedules.date', '>=', $today);

        // Menambahkan pencarian berdasarkan parameter search
        if ($request->has('search') && $request->search['value']) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('lesson_schedules.lesson_code', 'like', "%{$searchValue}%")
                ->orWhere('user_profiles.name', 'like', "%{$searchValue}%")
                ->orWhere('time_slots.start_time', 'like', "%{$searchValue}%");
            });
        }

        // Filter berdasarkan tanggal jika ada
        if ($request->has("date") && $request->date) {
            $filterDate = Carbon::parse($request->date)->format('Y-m-d');
            $query->whereDate('lesson_schedules.date', '=', $filterDate);
        }

        // Filter berdasarkan waktu jika ada
        if ($request->has("time_slot_id") && $request->time_slot_id
        ) {
            $query->where('lesson_schedules.time_slot_id', '=', $request->time_slot_id);
        }

        return DataTables::eloquent($query)
        ->addColumn("lesson_time", function ($booking) {
            $scheduleDate = Carbon::parse($booking->lessonSchedule->date)->format('d-m-Y');
            $scheduleTime = date("H:i", strtotime($booking->lessonSchedule->timeSlot->start_time));
            return "<strong>" . $scheduleDate . "</strong><br>" . $scheduleTime;
        })
        ->addColumn("lesson_code", function ($booking) {
            return $booking->lessonSchedule->lesson_code;
        })
        ->addColumn("booked_at", function ($booking) {
            $scheduleDate = Carbon::parse($booking->created_at);
            $formattedDate = $scheduleDate->format('d-m-Y');
            $formattedTime = $scheduleDate->format('H:i');
            return "<strong>" . $formattedDate . "</strong><br>" . $formattedTime;
        })
            ->rawColumns(["lesson_time", "booked_at"])
            ->make(true);
    }

    public function edit(User $user)
    {
        // Eager load relasi profile dan roles
        $user->load(["profile", "roles"]);

        // Ambil role pertama dari roles yang dimiliki user (jika ada)
        $roleId = optional($user->roles->first())->id;

        // Gunakan compact untuk merangkum variabel ke view
        $action = route("users.update", $user->id);

        return view("dashboard.users.form.form", compact("user", "action", "roleId"))
            ->with([
                "title_page" => "Ohana Pilates | Update User Profile",
                "method" => "POST"
            ]);
    }

    public function update(User $user, UpdateUserRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();

            // Update data di tabel 'users'
            $user = User::findOrFail($user->id);
            $user->name = $validated["name"];

            // Jika password diisi, update password
            if ($request->has('password') && $request->password) {
                $user->password = bcrypt($request->password);  // Hash password
            }

            $user->save();

            // Update data di tabel 'profiles'
            $profile = UserProfile::where("user_id", $user->id)->firstOrFail();

            // Menangani upload gambar
            if ($request->hasFile("profile_picture")) {
                // Hapus gambar lama jika ada
                if ($profile->profile_picture) {
                    // Menghapus file lama dari storage
                    Storage::disk("public")->delete($profile->profile_picture);
                }

                // Menyimpan gambar baru di storage
                $imageName = uniqid() . "." . $request->profile_picture->extension();

                // Simpan gambar di folder 'images/profile' di storage
                $path = $request->file("profile_picture")->storeAs("images/profile", $imageName, "public");

                // Simpan path gambar baru di database
                $profile->profile_picture = $path; // Simpan path seperti 'images/profile/imagename.extension'
            }

            // Update profile data lainnya
            $profile->gender = $validated["gender"];
            $profile->phone = $validated["phone"];
            $profile->address = isset($validated["address"]) && !empty($validated["address"]) ? $validated["address"] : null;
            $profile->save();

            // Update data di tabel 'user_roles'
            UserRole::where("user_id", $user->id)->update([
                "role_id" => $validated["role"]
            ]);

            DB::commit();

            alert()->success("Yeay!", "Successfully updated user data.");
            return redirect()->route("users.index");
        } catch (\Exception $e) {
            Log::error("Error updating user in UserController@update: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while updating user data, please try again.");
            return redirect()->back();
        }
    }

    public function destroy(User $user)
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($user->id);

            $user->delete();

            DB::commit();

            alert()->success("Yeay!", "Successfully deleted user data.");
            return redirect()->route("users.index");
        } catch (\Exception $e) {
            Log::error("Error deleted user in UserController@destroy: " . $e->getMessage());
            DB::rollBack();
            alert()->error("Oppss...", "An error occurred while deleted user data, please try again.");
            return redirect()->back();
        }
    }
}
