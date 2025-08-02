<div class="alert alert-info" style="font-size:16px; font-weight:bold; text-align:center; margin:10px;">
    💰 Tasa Oficial BCV: 1 USD = {$bcv_rate} Bs
</div>
<th>Precio (USD)</th>
<th>Precio (Bs)</th>
...
<td>{$ds.price}</td>
<td>{($ds.price * $bcv_rate)|number_format:2:",":"."}</td>
