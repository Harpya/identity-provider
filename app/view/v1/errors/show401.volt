<div class="jumbotron">
    <h1>Unauthorized</h1>

    {%- if msg -%}
    <p><strong>Reason:</strong> {{ msg }} </p>
    {%- endif -%}


    <p>You don't have access to this option. Contact an administrator</p>

    <p>{{ link_to('index', 'Home', 'class': 'btn btn-primary') }}</p>
</div>