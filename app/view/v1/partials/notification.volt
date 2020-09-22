{% if msg %}

{% if error %}
<div class="alert alert-danger fade show">
    {% else %}
    <div class="alert alert-info fade show">
        {% endif %}

        {{ msg }}

    </div>
    {% endif %}