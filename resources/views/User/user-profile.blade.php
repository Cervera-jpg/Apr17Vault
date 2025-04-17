@extends('layouts.user_type.auth')

@section('content')
  <div class="main-content position-relative bg-gray-100 max-height-vh-100 h-100">
    <div class="container-fluid">
      <!-- Profile Header Section -->
      <div class="page-header min-height-300 border-radius-xl mt-4" style="background-image: url('../assets/img/curved-images/curved0.jpg'); background-position-y: 50%;">
        <span class="mask bg-gradient-primary opacity-6"></span>
      </div>

      <!-- Main Profile Card -->
      <div class="card card-body blur shadow-blur mx-4 mt-n6 overflow-hidden">
        <div class="row gx-4">
          <div class="col-auto">
            <div class="avatar avatar-xl position-relative">
              <img src="../assets/img/default-avatar.png" alt="profile_image" class="w-100 border-radius-lg shadow-sm">
            </div>
          </div>
          <div class="col-auto my-auto">
            <div class="h-100">
              <h5 class="mb-1">{{ Auth::user()->name }}</h5>
              <p class="mb-0 font-weight-bold text-sm">User</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="container-fluid py-4">
      <div class="row">
        <!-- Profile Information -->
        <div class="col-12 col-xl-6">
          <div class="card h-100">
            <div class="card-header pb-0 p-3">
              <h6 class="mb-0">Profile Information</h6>
            </div>
            <div class="card-body p-3">
              <ul class="list-group">
                <li class="list-group-item border-0 ps-0 pt-0 text-sm"><strong class="text-dark">Full Name:</strong> &nbsp; {{ Auth::user()->name }}</li>
                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Email:</strong> &nbsp; {{ Auth::user()->email }}</li>
                <li class="list-group-item border-0 ps-0 text-sm"><strong class="text-dark">Member Since:</strong> &nbsp; {{ Auth::user()->created_at->format('M Y') }}</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Account Settings -->
        <div class="col-12 col-xl-6">
          <div class="card h-100">
            <div class="card-header pb-0 p-3">
              <h6 class="mb-0">Account Settings</h6>
            </div>
            <div class="card-body p-3">
              <form action="{{ route('user-profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                  <label for="name" class="form-control-label">Name</label>
                  <input class="form-control" type="text" name="name" id="name" value="{{ Auth::user()->name }}">
                </div>
                <div class="form-group">
                  <label for="email" class="form-control-label">Email</label>
                  <input class="form-control" type="email" name="email" id="email" value="{{ Auth::user()->email }}">
                </div>
                <div class="form-group">
                  <label for="password" class="form-control-label">New Password</label>
                  <input class="form-control" type="password" name="password" id="password">
                </div>
                <div class="form-group">
                  <label for="password_confirmation" class="form-control-label">Confirm New Password</label>
                  <input class="form-control" type="password" name="password_confirmation" id="password_confirmation">
                </div>
                <div class="text-end mt-4">
                  <button type="submit" class="btn btn-primary mb-0">Save Changes</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      @include('layouts.footers.auth.footer')
    </div>
  </div>

  <!-- Custom styles for user profile -->
  <style>
    .avatar-xl {
      width: 74px;
      height: 74px;
    }
    .border-radius-lg {
      border-radius: 0.75rem;
    }
    .form-control-label {
      margin-bottom: 0.5rem;
      font-size: 0.875rem;
      font-weight: 600;
    }
    .form-control {
      margin-bottom: 1rem;
    }
    .btn-primary {
      background-color: #821131;
      border-color: #821131;
    }
    .btn-primary:hover {
      background-color: #6a0e28;
      border-color: #6a0e28;
    }
  </style>
@endsection 