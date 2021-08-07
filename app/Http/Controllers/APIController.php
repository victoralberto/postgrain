<?php

namespace App\Http\Controllers;

use App\Lembretes;
use App\Notificacao;
use Illuminate\Http\Request;

class APIController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $json = array();

        $notificacao = Notificacao::all();

        if (count($notificacao) == 0) {
            return response()->json(["message" => "Não há lembretes cadastrados."]);
        }

        for ($i = 0; $i < count($notificacao); $i++) {
            $lembrete = Lembretes::where("id", $notificacao[$i]->id_lembrete)->first();
            if ($lembrete->ativo != false) {
                if ($notificacao[$i]->status == 1) {
                    $notificacao[$i]->status = "agendado";
                } elseif ($notificacao[$i]->status == 2) {
                    $notificacao[$i]->status = "pendente";
                } else {
                    $notificacao[$i]->status = "concluido";
                }

                if ($lembrete->repetir == 1) {
                    $lembrete->repetir = "nunca";
                } elseif ($lembrete->repetir == 2) {
                    $lembrete->repetir = "semanal";
                } else {
                    $lembrete->repetir = "mensal";
                }

                $json[$i] = [
                    "id" => $notificacao[$i]->id,
                    "username" => $lembrete->username,
                    "titulo" => $lembrete->titulo,
                    "descricao" => $lembrete->descricao,
                    "data_lembrete" => $lembrete->data_lembrete,
                    "repetir" => $lembrete->repetir,
                    "status" => $notificacao[$i]->status
                ];
            }
        }
        return response()->json($json);
    }

    public function notificacao()
    {
        $json = array();

        $all_lembretes = Lembretes::all();
        $notificacoes = Notificacao::all();

        $date_today = date("Y-m-d H:i");

        foreach ($all_lembretes as $lembrete) {
            if ($lembrete->ativo == true) {
                switch ($lembrete->repetir) {
                    case 2:
                        if (date("D", strtotime($date_today)) == date("D", strtotime($lembrete->data_lembrete))) {
                            $verify = Notificacao::where("lembrete", $date_today)->where("id_lembrete", $lembrete->id)->first();
                            if (!$verify) {
                                $notificacao = new Notificacao();
                                $notificacao->id_lembrete = $lembrete->id;
                                $notificacao->lembrete = $date_today;
                                $notificacao->save();
                                $notificacoes = Notificacao::all();
                            }
                        }
                        break;
                    case 3:
                        if (date("d", strtotime($date_today)) == date("d", strtotime($lembrete->data_lembrete))) {
                            $verify = Notificacao::where("lembrete", $date_today)->where("id_lembrete", $lembrete->id)->first();
                            if (!$verify) {
                                $notificacao = new Notificacao();
                                $notificacao->id_lembrete = $lembrete->id;
                                $notificacao->lembrete = $date_today;
                                $notificacao->save();
                                $notificacoes = Notificacao::all();
                            }
                        }
                        break;
                }
            }
        }

        $day_today = date("d", strtotime($date_today));
        $current_month = date("m", strtotime($date_today));
        $current_year = date("Y", strtotime($date_today));
        $hour = date("H:i", strtotime($date_today));

        for ($i = 0; $i < count($notificacoes); $i++) {
            $lembretes = Lembretes::where("id", $notificacoes[$i]->id_lembrete)->get();
            foreach ($lembretes as $lembrete) {
                if ($lembrete->ativo == true) {

                    $notificacoes_day = date("d", strtotime($notificacoes[$i]->lembrete));
                    $notificacoes_month = date("m", strtotime($notificacoes[$i]->lembrete));
                    $notificacoes_year = date("Y", strtotime($notificacoes[$i]->lembrete));


                    if ($lembrete->repetir == 1 && $notificacoes[$i]->status != 3) {
                        switch ($date_today) {
                            case $day_today == $notificacoes_day && $current_month == $notificacoes_month && $current_year == $notificacoes_year && $hour == "08:00":
                                $json[$i] = [
                                    "id" => $notificacoes[$i]->id,
                                    "data_lembrete" => $notificacoes[$i]->lembrete,
                                    "username" => $lembrete->username,
                                    "titulo" => $lembrete->titulo,
                                    "descricao" => $lembrete->descricao,
                                ];
                                break;
                            case $day_today > $notificacoes_day && $current_month == $notificacoes_month && $current_year == $notificacoes_year && $hour == "08:00":
                                if ($notificacoes[$i]->status == 1) {
                                    $notificacoes[$i]->status = 2;
                                    $notificacoes[$i]->save();

                                    $json[$i] = [
                                        "id" => $notificacoes[$i]->id,
                                        "data_lembrete" => $notificacoes[$i]->lembrete,
                                        "username" => $lembrete->username,
                                        "titulo" => $lembrete->titulo,
                                        "descricao" => $lembrete->descricao,
                                    ];
                                }
                                $json[$i] = [
                                    "id" => $notificacoes[$i]->id,
                                    "data_lembrete" => $notificacoes[$i]->lembrete,
                                    "username" => $lembrete->username,
                                    "titulo" => $lembrete->titulo,
                                    "descricao" => $lembrete->descricao,
                                ];
                                break;
                            case $day_today < $notificacoes_day && $current_month > $notificacoes_month && $current_year == $notificacoes_year && $hour == "08:00":
                                if ($notificacoes[$i]->status == 1) {
                                    $notificacoes[$i]->status = 2;
                                    $notificacoes[$i]->save();

                                    $json[$i] = [
                                        "id" => $notificacoes[$i]->id,
                                        "data_lembrete" => $notificacoes[$i]->lembrete,
                                        "username" => $lembrete->username,
                                        "titulo" => $lembrete->titulo,
                                        "descricao" => $lembrete->descricao,
                                    ];
                                }
                                $json[$i] = [
                                    "id" => $notificacoes[$i]->id,
                                    "data_lembrete" => $notificacoes[$i]->lembrete,
                                    "username" => $lembrete->username,
                                    "titulo" => $lembrete->titulo,
                                    "descricao" => $lembrete->descricao,
                                ];
                                break;
                            case $day_today > $notificacoes_day && $current_month > $notificacoes_month && $current_year == $notificacoes_year && $hour == "08:00":
                                if ($notificacoes[$i]->status == 1) {
                                    $notificacoes[$i]->status = 2;
                                    $notificacoes[$i]->save();

                                    $json[$i] = [
                                        "id" => $notificacoes[$i]->id,
                                        "data_lembrete" => $notificacoes[$i]->lembrete,
                                        "username" => $lembrete->username,
                                        "titulo" => $lembrete->titulo,
                                        "descricao" => $lembrete->descricao,
                                    ];
                                }
                                $json[$i] = [
                                    "id" => $notificacoes[$i]->id,
                                    "data_lembrete" => $notificacoes[$i]->lembrete,
                                    "username" => $lembrete->username,
                                    "titulo" => $lembrete->titulo,
                                    "descricao" => $lembrete->descricao,
                                ];
                                break;
                            case $day_today == $notificacoes_day && $current_month == $notificacoes_month && $current_year > $notificacoes_year && $hour == "08:00":
                                if ($notificacoes[$i]->status == 1) {
                                    $notificacoes[$i]->status = 2;
                                    $notificacoes[$i]->save();

                                    $json[$i] = [
                                        "id" => $notificacoes[$i]->id,
                                        "data_lembrete" => $notificacoes[$i]->lembrete,
                                        "username" => $lembrete->username,
                                        "titulo" => $lembrete->titulo,
                                        "descricao" => $lembrete->descricao,
                                    ];
                                }
                                $json[$i] = [
                                    "id" => $notificacoes[$i]->id,
                                    "data_lembrete" => $notificacoes[$i]->lembrete,
                                    "username" => $lembrete->username,
                                    "titulo" => $lembrete->titulo,
                                    "descricao" => $lembrete->descricao,
                                ];
                                break;
                            case $day_today > $notificacoes_day && $current_month < $notificacoes_month && $current_year > $notificacoes_year && $hour == "08:00":
                                if ($notificacoes[$i]->status == 1) {
                                    $notificacoes[$i]->status = 2;
                                    $notificacoes[$i]->save();

                                    $json[$i] = [
                                        "id" => $notificacoes[$i]->id,
                                        "data_lembrete" => $notificacoes[$i]->lembrete,
                                        "username" => $lembrete->username,
                                        "titulo" => $lembrete->titulo,
                                        "descricao" => $lembrete->descricao,
                                    ];
                                }
                                $json[$i] = [
                                    "id" => $notificacoes[$i]->id,
                                    "data_lembrete" => $notificacoes[$i]->lembrete,
                                    "username" => $lembrete->username,
                                    "titulo" => $lembrete->titulo,
                                    "descricao" => $lembrete->descricao,
                                ];
                                break;
                            case $day_today < $notificacoes_day && $current_month < $notificacoes_month && $current_year > $notificacoes_year && $hour == "08:00":
                                if ($notificacoes[$i]->status == 1) {
                                    $notificacoes[$i]->status = 2;
                                    $notificacoes[$i]->save();

                                    $json[$i] = [
                                        "id" => $notificacoes[$i]->id,
                                        "data_lembrete" => $notificacoes[$i]->lembrete,
                                        "username" => $lembrete->username,
                                        "titulo" => $lembrete->titulo,
                                        "descricao" => $lembrete->descricao,
                                    ];
                                }
                                $json[$i] = [
                                    "id" => $notificacoes[$i]->id,
                                    "data_lembrete" => $notificacoes[$i]->lembrete,
                                    "username" => $lembrete->username,
                                    "titulo" => $lembrete->titulo,
                                    "descricao" => $lembrete->descricao,
                                ];
                                break;
                        }

                    } elseif ($lembrete->repetir == 2 && $notificacoes[$i]->status != 3) {

                        $notificacoes_dayWeek = date("N", strtotime($notificacoes[$i]->lembrete));
                        $dayWeek_today = date("N", strtotime($date_today));

                        switch ($dayWeek_today) {
                            case $dayWeek_today === $notificacoes_dayWeek && $hour == "08:00":
                                $json[$i] = [
                                    "id" => $notificacoes[$i]->id,
                                    "data_lembrete" => $notificacoes[$i]->lembrete,
                                    "username" => $lembrete->username,
                                    "titulo" => $lembrete->titulo,
                                    "descricao" => $lembrete->descricao,
                                ];
                                break;
                            case $dayWeek_today > $notificacoes_dayWeek && $hour == "08:00":
                                if ($notificacoes[$i]->status == 1) {
                                    $notificacoes[$i]->status = 2;
                                    $notificacoes[$i]->save();

                                    $json[$i] = [
                                        "id" => $notificacoes[$i]->id,
                                        "data_lembrete" => $notificacoes[$i]->lembrete,
                                        "username" => $lembrete->username,
                                        "titulo" => $lembrete->titulo,
                                        "descricao" => $lembrete->descricao,
                                    ];
                                }
                                $json[$i] = [
                                    "id" => $notificacoes[$i]->id,
                                    "data_lembrete" => $notificacoes[$i]->lembrete,
                                    "username" => $lembrete->username,
                                    "titulo" => $lembrete->titulo,
                                    "descricao" => $lembrete->descricao,
                                ];
                                break;
                        }

                    } elseif ($lembrete->repetir == 3 && $notificacoes[$i]->status != 3) {
                        switch ($day_today) {
                            case $day_today == $notificacoes_day && $hour == "08:00":
                                $json[$i] = [
                                    "id" => $notificacoes[$i]->id,
                                    "data_lembrete" => $notificacoes[$i]->lembrete,
                                    "username" => $lembrete->username,
                                    "titulo" => $lembrete->titulo,
                                    "descricao" => $lembrete->descricao,
                                ];
                                break;
                            case $day_today > $notificacoes_day && $hour == "08:00":
                                if ($notificacoes[$i]->status == 1) {
                                    $notificacoes[$i]->status = 2;
                                    $notificacoes[$i]->save();

                                    $json[$i] = [
                                        "id" => $notificacoes[$i]->id,
                                        "data_lembrete" => $notificacoes[$i]->lembrete,
                                        "username" => $lembrete->username,
                                        "titulo" => $lembrete->titulo,
                                        "descricao" => $lembrete->descricao,
                                    ];
                                }
                                $json[$i] = [
                                    "id" => $notificacoes[$i]->id,
                                    "data_lembrete" => $notificacoes[$i]->lembrete,
                                    "username" => $lembrete->username,
                                    "titulo" => $lembrete->titulo,
                                    "descricao" => $lembrete->descricao,
                                ];
                                break;
                        }
                    }
                }
            }
        }
        return response()->json($json);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Adicionar condição para status (range de 1 à 3)
        $lembrete = new Lembretes();

        if ($request->repetir > 0 && $request->repetir < 4) {
            $lembrete->username = $request->username;
            $lembrete->titulo = $request->titulo;
            $lembrete->descricao = $request->descricao;
            $lembrete->data_lembrete = $request->data_lembrete;
            $lembrete->repetir = $request->repetir;

            if ($lembrete->save()) {
                $notificacao = new Notificacao();
                $notificacao->id_lembrete = $lembrete->id;
                $notificacao->lembrete = $lembrete->data_lembrete;

                if ($notificacao->save()) {
                    return response()->json(["message" => "Lembrete adicionado com sucesso!!"]);
                } else {
                    return response()->json(["message" => "Erro ao agendar lembrete."]);
                }
            } else {
                return response()->json(["message" => "Erro ao adicionar lembrete."]);
            }

        } else {
            return response()->json(["message" => "A recorrência deve conter um valor de 1 à 2."]);
        }
    }

    public function concluir_lembrete($id)
    {
        $notificacao = Notificacao::where("id", $id)->first();
        $notificacao->status = 3;
        if ($notificacao->save()) {
            return response()->json(["Message" => "Lembrete marcado com concluído!!"]);
        } else {
            return response()->json(["Message" => "Erro ao tentar marcar o lembrete com concluído!!"]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $notificacao = Notificacao::where("id", $id)->first();
        $lembrete = Lembretes::where("id", $notificacao->id_lembrete)->first();

        if (isset($lembrete)) {
            return response()->json([
                "id" => $notificacao->id,
                "data_lembrete" => $lembrete->data_lembrete,
                "username" => $lembrete->username,
                "titulo" => $lembrete->titulo,
                "descricao" => $lembrete->descricao,
                "repetir" => $lembrete->repetir,
                "status" => $notificacao->status
            ]);
        } else {
            return response()->json(["Message" => "Erro ao tentar visualizar as informações do lembrete."]);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $notificacao = Notificacao::where("id", $id)->first();
        $lembrete = Lembretes::where("id", $notificacao->id_lembrete)->first();

        if (isset($request->username)) {
            $lembrete->username = $request->username;
        }

        if (isset($request->titulo)) {
            $lembrete->titulo = $request->titulo;
        }

        if (isset($request->descricao)) {
            $lembrete->descricao = $request->descricao;
        }

        if (isset($request->data_lembrete)) {
            $lembrete->data_lembrete = $request->data_lembrete;
        }

        if (isset($request->repetir)) {
            $lembrete->repetir = $request->repetir;
        }

        if ($lembrete->save()) {
            return response()->json(["Message" => "Lembrete atualizado com sucesso!!"]);
        } else {
            return response()->json(["Message" => "Erro ao tentar atualizar o lembrete"]);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $notificacao = Notificacao::where("id", $id)->first();
        $lembrete = Lembretes::where("id", $notificacao->id_lembrete)->first();

        $lembrete->ativo = false;
        if ($lembrete->save()) {
            return response()->json(["Message" => "Lembrete excluído com sucesso!!"]);
        } else {
            return response()->json(["Message" => "Erro ao tentar excluir Lembrete."]);
        }
    }
}
