<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Modulo_ciclo;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonPost;


class PanelControlProgramacionController extends BaseController
{

    
    protected $model = 'Modulo_ciclo';
    protected $gridFields = ['Xciclo', 'Xmodulo', 'estado', 'situacion'];
    
   
    protected function search()
    {
        if (UserisAllow(config('constants.rol.direccion')))
            return Modulo_ciclo::all();
        else 
            return Modulo_ciclo::Departamento(AuthUser()->departamento)
                    ->get();
    }

}