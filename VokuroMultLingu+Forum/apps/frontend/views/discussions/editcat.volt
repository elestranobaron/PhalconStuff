
<form method="post" autocomplete="off">

<ul class="pager">
    <li class="previous pull-left">
        {{ link_to("settings/advanced", "&larr; Go Back") }}{#profiles au lieu de settings#}
    </li>
    <li class="pull-right">
        {{ submit_button("Save", "class": "btn btn-success") }}
    </li>
</ul>

{{ content() }}

<div class="center scaffold">

    <h2>Edit Categories</h2>

    <ul class="nav nav-tabs">
        <li class="active"><a href="#A" data-toggle="tab">Basic</a></li>
        <li><a href="#B" data-toggle="tab">Users</a></li>
    </ul>

    <div class="tabbable">
        <div class="tab-content">
            <div class="tab-pane active" id="A">

                {{ form.render("id") }}

                <div class="clearfix">
                    <label for="name">Name</label>
                    {{ form.render("name") }}
                </div>
		<div class="clearfix">
			<label for="sub">Sub of</label>
			{{ form.render("sub")}}
		</div>
     {#           <div class="clearfix">
                    <label for="no_slug">Slug</label>
                    {{ form.render("no_slug") }}
                </div>                                                       #}

           {#     <div class="clearfix">
                    <label for="active">Active?</label>
                    {{ form.render("active") }}
                </div>#}

            </div>

            <div class="tab-pane" id="B">
                <p>
                    <table class="table table-bordered table-striped" align="center">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                        {#        <th>Active?</th>#}
                            </tr>
                        </thead>
                        <tbody>
                        {% for category in profile.categories %}
                            <tr>
                                <td>{{ category.id }}</td>
                                <td>{{ category.name }}</td>
                {#                <td>{{ category.no_slug }}</td>                     #}
                          {#      <td>{{ category.active == 'Y' ? 'Yes' : 'No' }}</td>#}
                                <td width="12%">{{ link_to("settings/edit/" ~ category.id, '<i class="icon-pencil"></i> Edit', "class": "btn") }}</td>
                                <td width="12%">{{ link_to("settings/delete/" ~ category.id, '<i class="icon-remove"></i> Delete', "class": "btn") }}</td>
                            </tr>
                        {% else %}
                            <tr><td colspan="3" align="center">There are no categorys assigned to this profile</td></tr>
                        {% endfor %}
                        </tbody>
                    </table>
                </p>
            </div>

        </div>
    </div>

    </form>
</div>
