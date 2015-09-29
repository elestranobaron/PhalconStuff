
<form method="post" autocomplete="off">

<ul class="pager">
    <li class="previous pull-left">
        {{ link_to("settings", "&larr; Go Back") }}
    </li>
    <li class="pull-right">
        {{ submit_button("Save", "class": "btn btn-success") }}
    </li>


{#{{ content() }}#}
	<div class="container start-discussion"><!--"center scaffold">-->
    	<h2>Create a Category</h2>
</div>
	    <div class="form-group"><!--clearfix-->
        	<label for="name">Name</label>
        	{{ form.render("name") }}
{#		<?php $form = new Phalcon\Forms\Form();$form.render("name")?>#}
    </div>
	<div class="form-group">
		<label for="description">Description</label>
		{{form.render("description")}}
        </div>
	<div class="clearfix">
		<label for="sub">Sub of</label>
		{{ form.render("sub") }}
	</div>
<!--
    <div class="clearfix">
        <label for="active">Active?</label>
        {{ form.render("active") }}
    </div>-->

<!--</div>-->
</ul>

</form>
