// Actualizar cada 6 horas máximo
$last_update = ORM::for_table('bcv_rate')->find_one(1);
if (!$last_update || (time() - strtotime($last_update->updated_at)) > 21600) {
    updateBCVRate();
}

$bcv_rate = getBCVRate();
$ui->assign('bcv_rate', $bcv_rate);