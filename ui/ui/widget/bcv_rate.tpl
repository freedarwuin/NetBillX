{* --- Depuración BCV --- *}
<fieldset style="border:1px solid #f00; padding:10px; margin-bottom:20px;">
    <legend style="color:#f00; font-weight:bold;">DEBUG BCV</legend>

    <div><strong>bcv_rate:</strong> {$bcv_rate|default:'NULL'}</div>

    <div><strong>bcv_history:</strong></div>
    <pre>
{$bcv_history|@print_r}
    </pre>

</fieldset>