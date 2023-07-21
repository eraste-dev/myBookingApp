@extends('layouts.app')

@section('content')
    <form id="upload-form" method="post" action="{{ route('media.store') }}" enctype="multipart/form-data"
        class="form-horizontal">
        @csrf
        <div class="form-group mb-3">
            <label for="file" class="col-sm-2 control-label">Choisir un ou plusieurs fichiers</label>
            <div class="col-sm-10">
                <input type="file" id="file" name="file[]" multiple class="form-control">
            </div>
        </div>

        <div class="form-group mb-5">
            <label for="file" class="col-sm-2 control-label">emplacements</label>
            <div class="col-sm-10">
                <input value="hotels" name="destination" class="form-control" >
            </div>
        </div>

        <div class="form-group mb-5 d-none">
            <label for="userId" class="col-sm-2 control-label">ID de l'utilisateur</label>
            <div class="col-sm-10">
                <input type="text" id="userId" name="user_id" value="{{ Auth::user()->id }}" class="form-control" readonly>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">Envoyer</button>
            </div>
        </div>
    </form>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript">
        $('#file').on('change', function() {
            // console.log('Un fichier a été sélectionné');
            // console.log("{{ route('media.store') }}");
        });

        $(document).ready(function(e) {
            token =
                "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYXBpL3YxL2xvZ2luIiwiaWF0IjoxNjg5OTQ1NzI2LCJleHAiOjE2OTAwMzIxMjYsIm5iZiI6MTY4OTk0NTcyNiwianRpIjoiNktNWlpoMjRWOVJ5QUdwaSIsInN1YiI6IjEiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.uoY-wFF7HiMVEc7wPSUlACO_OPxRh66Ro_kFlhiSf7k";

            $('#upload-form').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('media.store') }}",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader("Authorization", "Baerer " + token);
                    },
                    success: function(response) {
                        console.log(response);
                        alert('Fichiers uploadés avec succès');
                    },
                    error: function(error) {
                        console.log(error.responseJSON);
                        alert(error.statusText);
                    }
                });
            });
        });
    </script>
@endsection
