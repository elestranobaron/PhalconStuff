{{ content() }}

<ul class="pager">
    <li class="previous pull-left">
        {{ link_to("settings", "&larr; Go Back") }}
    </li>
    <li class="pull-right">
        {{ link_to("settings/newcat", "Create categories", "class": "btn") }} {# btn-primary") }}#}
    </li>


{% for category in page.items %}
{% if loop.first %}
<table class="table table-bordered table-striped" align="center">
    <thead>
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>Sub Cat of</th>
{#            <th>Active?</th> #}
        </tr>
    </thead>
{% endif %}
    <tbody>
        <tr>
            <td>{{ category.id }}</td>
            <td>{{ category.name }}</td>
            <td>{{ category.sub }}</td>
{#            <td>{{ profile.active == 'Y' ? 'Yes' : 'No' }}</td>#}
            <td width="12%">{{ link_to("settings/editcat/" ~ category.id, '<i class="icon-pencil"></i> Edit', "class": "btn") }}</td>
            <td width="12%">{{ link_to("settings/deletecat/" ~ category.id, '<i class="icon-remove"></i> Delete', "class": "btn") }}</td>
        </tr>
    </tbody>
{% if loop.last %}
    <tbody>
        <tr>
            <td colspan="10" align="right">
                <div class="btn-group">
                    {{ link_to("settings/advanced", '<i class="icon-fast-backward"></i> First', "class": "btn") }}
                    {{ link_to("settings/advanced?page=" ~ page.before, '<i class="icon-step-backward"></i> Previous', "class": "btn ") }}
                    {{ link_to("settings/advanced?page=" ~ page.next, '<i class="icon-step-forward"></i> Next', "class": "btn") }}
                    {{ link_to("settings/advanced?page=" ~ page.last, '<i class="icon-fast-forward"></i> Last', "class": "btn") }}
                    <span class="help-inline">{{ page.current }}/{{ page.total_pages }}</span>
                </div>
            </td>
        </tr>
    <tbody>
</table>
{% endif %}
{% else %}
    No categories are recorded
{% endfor %}
</ul>
