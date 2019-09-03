@extends('layouts.pdf')
@section('content')
@php
   $agrupados = $todos->groupBy('idFct')
@endphp
    @foreach ($agrupados as $grupo)
    <div class="page">
        @include('pdf.fct.partials.cabecera')
        <br/>
        <table border="1" style="margin-bottom: 10px">
            <colgroup><col width="449"/><col width="329"/></colgroup>
            <tr>
                <td style="text-align:left;width:23.283cm;padding-left: 5px "><strong>Revisió actual:</strong><span> 7</span></td>
                <td style="text-align:left;width:20.523cm;padding-left: 5px  "><strong>Data revisió actual:</strong><span> 15-12-2017</span></td>
            </tr>
            <tr>
                <td style="text-align:left;padding-left: 5px " colspan="2"><strong>Objectiu:</strong><span> Facilitar a l'instructor de l'empresa el seguiment dels alumnes durant l'FCT.</span></td>
            </tr>
        </table>
        <br/>
        <table border="1" cellspacing="0" cellpadding="0">
            <tr>
                <td style="text-align:left;width:30.283cm;padding-left: 5px;font-size: 0.9em "><strong>Empresa:</strong> <span>{{$grupo->first()->Fct->Colaboracion->Centro->nombre}}</span></td>
                <td style="text-align:left;width:30.2833cm;padding-left: 5px;font-size: 0.9em "><strong>Cicle:</strong> <span>{{$grupo->first()->Fct->Colaboracion->Ciclo->ciclo}}</span></td>
            </tr>
            <tr>
                <td style="text-align:left;width:30.283cm;padding-left: 5px;font-size: 0.9em "><strong>Instructors:</strong> <span>
                      {{$grupo->first()->Fct->XInstructor}} @foreach ($grupo->first()->Fct->Colaboradores as $instructor)  ,{{$instructor->nombre}} @endforeach
                    </span></td>
                <td style="text-align:left;width:30.2833cm;padding-left: 5px;font-size: 0.9em "><strong>Tutor:</strong> <span>{{AuthUser()->FullName}}</span></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:left;width:30.283cm;padding-left: 5px;font-size: 0.9em "><strong>Alumnes:</strong>
                    @foreach ($grupo as $alumno)
                        <span>{{ $alumno->Alumno->FullName }} </span>
                    @endforeach
                </td>
            </tr>
        </table>
        <br/>
        <table border="1" cellspacing="0" cellpadding="0">
            <tr>
                <td colspan="3" style="text-align:center;width:30.283cm;padding-left: 5px;font-size: 0.9em "><strong>Seguiments:</strong>
                </td>
            </tr>
            <tr>
                <td>Data</td>
                <td>Medi</td>
                <td>Tipus Contacte</td>
            </tr>
            @php
                $contactFct = \Intranet\Entities\Activity::contactes()->relationId($grupo->first()->idFct)->orderBy('created_at')->get();
            @endphp
            @foreach ($contactFct as $contact)
                <tr>
                    <td style="text-align:left;padding-left: 5px;font-size: 0.9em "><strong>{{fechaCurta($contact->created_at)}}</strong></td>
                    <td style="text-align:left;padding-left: 5px;font-size: 0.9em "><strong>{{$contact->action}}</strong></td>
                    <td style="text-align:left;padding-left: 5px;font-size: 0.9em "><strong>{{$contact->comentari}}</strong></td>
                </tr>
            @endforeach
        </table>
        <table border='1' style="width:350px;height:100px;float:left;margin-bottom: 20px">
            <tr><td style='text-align: left; vertical-align: top '><strong>Signatura Tutor:</strong></td></tr>
        </table>
        <table border='1' style="width:350px;height:100px;float:left;margin-bottom: 20px">
            <tr><td style='text-align: left; vertical-align: top '><strong>Signatura Instructor:</strong></td></tr>
        </table>
        <div style="float:left;">
            <p style="font-size: x-small; font-weight: bold ">
               En cas d'avaluació negativa s'haurà d'obrir un registre de Queixes i Suggerències (PG06-01) per a resoldre el problema i realitzar un seguiment d'aquest.<br/>
                *Contacte amb l'instructor a l'empresa o via telefònica.
            </p>
        </div>
    </div>
    @endforeach
@endsection