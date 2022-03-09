<ul class="messages colaboracion">
    @foreach ($contactAl as $contacto)
        @php($alumno = \Intranet\Entities\AlumnoFct::find($contacto->model_id))
        <li>
            <div class="message_wrapper">
                <h5>
                    <em class="fa fa-calendar user-profile-icon"></em> {!! $contacto->created_at !!}
                    <em class="fa fa-envelope user-profile-icon"></em> {{$contacto->document}}
                    <em class="fa fa-user user-profile-icon"></em> {{$alumno->fullName??''}}
                </h5>
            </div>
        </li>
    @endforeach
</ul>