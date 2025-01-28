<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.head-main')
    @include('layouts.head')
    @include('layouts.head-style')
    <title>Login | SIM KKN UAD</title>
</head>

<body>
    <div class="container">
        <h2>Choose Role</h2>
        <form action="{{ route('set.role') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="role">Select Role:</label>
                <select name="role_id" id="role" class="form-control">
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->role->nama_role }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Set Role</button>
        </form>
    </div>

    <!-- Modal Bootstrap -->
    <div class="modal fade" id="roleModal" tabindex="-1" role="dialog" aria-labelledby="roleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roleModalLabel">Choose Your Role</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('set.role') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="role">Select Role:</label>
                            <select name="role_id" id="role" class="form-control">
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->role->nama_role }} @if ($role->id_kkn != '')
                                            {{ ' (' . $role->kkn->nama . ')' }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Set Role</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
@include('layouts.vendor-scripts')
<script>
    $(document).ready(function() {
        $('#roleModal').modal('show');
    });
</script>

</html>
