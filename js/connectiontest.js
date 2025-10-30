document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('btnSentyTest');
    const resultDiv = document.getElementById('ajax_result');

    if (!btn) return;

    btn.addEventListener('click', function () {
        resultDiv.innerHTML = '<em>...</em>';

        fetch(M.cfg.wwwroot + '/admin/tool/sentry/ajax/testException.php', {
            method: 'GET',
            credentials: 'same-origin'
        })
            .then(response => response.text())
            .then(text => {
                resultDiv.innerHTML = '<pre>' + text + '</pre>';
            })
            .catch(err => {
                resultDiv.innerHTML = '<span style="color:red;">Error: ' + err.message + '</span>';
            });
    });
});