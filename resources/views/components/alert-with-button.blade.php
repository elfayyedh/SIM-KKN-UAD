<div>
    @if (session($sessionError))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session($sessionError) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @elseif (session($sessionSuccess))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session($sessionSuccess) }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>
