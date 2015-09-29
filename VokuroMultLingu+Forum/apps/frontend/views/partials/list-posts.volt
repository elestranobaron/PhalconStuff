<div class="container">

{#			'answerst': 'Sub categories',#}
{#2			{%- if (!gotsub and order == 'answerst') -%} Dans une logique ou l on n affiche que les categories il faut remplacer category.sub par gotsub
				{%- continue -%}
			{% endif -%}#}
	<ul class="nav nav-tabs">
		{%- set orders = [
			'new': 'All discussions',
			'hot': 'Hot',
			'unanswered': 'Unanswered',
			'my': 'My discussions',
			'answers':'My answers'
		] -%}
		{%- for order, label in orders -%}
			{%- if ((order == 'my' or order == 'answers') and !session.get('identity')) -%}
				{%- continue -%}
			{% endif -%}
			{%- if order == currentOrder -%}
				<li class="active">
			{%- else -%}
				<li>
			{%- endif -%}
				{{ link_to('discussions/' ~ order, label) }}
			</li>
		{%- endfor -%}
	</ul>
</div>
{#gotsub existe ou non#}
{%- if gotsub-%}
{{ flashSession.output() }}

<div class="clearfix">
	<div class="col-lg-9  center-block">
		<div class="panel panel-default">
		  <div class="panel-heading">Categories</div>


	 <table class="table">
        <thead>
          <tr>
            <th>#</th>
            <th class="col-lg-9">Category Name</th>
            <th></th>
            <th>Last Message</th>
          </tr>
        </thead>
        <tbody>

		{%- for categorie in categoriessub -%}
{#{%if !categorie.sub%}  [fallait le retirer]Ajout tres important pour n'ajouter simplement que les categories + securite a reflechir pour qu aumoins une ne disparaisse jamais#}
          <tr>
            <td>
            {%- if logged != '' -%}
            <?php if ($not_read[$categorie->id]->numRows() == 0) { ?>
			 {{ image("icon/new_none.png", "class": "img-rounded") }}
			<?php } else { ?>
			 {{ image("icon/new_some.png", "class": "img-rounded") }}
			<?php } ?>
            {%- else -%}
             {{ image("icon/new_none.png", "class": "img-rounded") }}
            {%- endif -%}
            </td>

		{#A revoir je sais plus pourquoi j avais tente ca#}
            {#<td>{%if !gotsub%}{{ link_to('category/' ~ categorie.id ~ '/' ~ categorie.slug, categorie.name) }}
			<br><small>{{ categorie.description }}</small>{%endif%}</td>#}

            <td>{{ link_to('category/' ~ categorie.id ~ '/' ~ categorie.slug, categorie.name) }}
			<br><small>{{ categorie.description }}</small></td>
            <td><?php echo count(\Phosphorum\Models\Posts::find("categories_id=".$categorie->id)); ?> Threads</td>
            <td>
			<?php
				if (count(\Phosphorum\Models\Posts::find("categories_id=".$categorie->id)) > 0) {
				echo $this->tag->linkTo("discussion/{$last_author[$categorie->id][0]->post1_id}/{$last_author[$categorie->id][0]->post1_slug}", $last_author[$categorie->id][0]->post1_title);
					echo '<br>@'.$last_author[$categorie->id][0]->name_user;
				} else {
					echo '---';
				}

			?></td>
          </tr>
{#{%endif%}#}
		{%- endfor -%}

        </tbody>
      </table>

		</div>
	</div>
</div>
{%-endif-%}

{%- if !gotsub-%}
{%- if posts|length -%}
<div class="container">
	<br/>
	<div align="center">
		<table class="table table-striped list-discussions" width="90%">
			<tr>
				<th> &nbsp; </th>
				<th width="40%">Topic</th>
				<th class="hidden-xs">Users</th>
				<th class="hidden-xs">Category</th>
				<th class="hidden-xs">Replies</th>
				<th class="hidden-xs">Views</th>
				<th class="hidden-xs">Created</th>
				<th class="hidden-xs">Last Reply</th>
			</tr>
		{%- for post in posts -%}
			<tr class="{% if (post.votes_up - post.votes_down) <= -3 %}post-negative{% endif %}">
			    <td>
                	{%- if logged != '' -%}
					<?php
						if (in_array($post->id, $readposts)) {
							echo Phalcon\Tag::imageInput(array("src" => "/icon/new_none.png", "width" => "24", "height" => "24", "class" => "img-rounded"));
						} else {
							echo Phalcon\Tag::imageInput(array("src" => "/icon/new_some.png", "width" => "24", "height" => "24", "class" => "img-rounded"));
						}
					?>
                    {%- else -%}
                     {{ image("icon/new_none.png", "width": "24", "height": "24", "class": "img-rounded") }}
                    {%- endif -%}
				</td>
				<td align="left">

					{%- if post.sticked == "Y" -%}
						<span class="glyphicon glyphicon-pushpin"></span>&nbsp;
					{%- endif -%}
					{{- link_to('discussion/' ~ post.id ~ '/' ~ post.slug, post.title|e) -}}
					{%- if post.accepted_answer == "Y" -%}
						&nbsp;<span class="label label-success">SOLVED</span>
					{%- else -%}
						{%- if post.canHaveBounty() -%}
							&nbsp;<span class="label label-info">BOUNTY</span>
						{%- endif -%}
					{%- endif -%}

				</td>
				<td class="hidden-xs">
					{%- cache "post-users-" ~ post.id -%}
						{%- for id, user in post.getRecentUsers() -%}
						 	<a href="{{ url("user/" ~ id ~ "/" ~ user[0]) }}" title="{{ user[0] }}">
								<img src="https://secure.gravatar.com/avatar/{{ user[1] }}?s=24&amp;r=pg&amp;d=identicon" width="24" height="24" class="img-rounded">
							</a>
						{%- endfor -%}
					{%- endcache -%}
				</td>
				<td class="hidden-xs">
					<span class="category">{{ link_to('category/' ~ post.category.id ~ '/' ~ post.category.slug, post.category.name) }}</span>
					
				</td>
				<td class="hidden-xs" align="center">
					<span class="big-number">{% if post.number_replies > 0 %}{{ post.number_replies }}{%endif %}</span>
				</td>
				<td class="hidden-xs" align="center">
					<span class="big-number">{{ post.getHumanNumberViews() }}</span>
				</td>
				<td class="hidden-xs">
					<span class="date">{{ post.getHumanCreatedAt() }}</span>
				</td>
				<td class="hidden-xs">
					<span class="date">{{ post.getHumanModifiedAt() }}</span>
				</td>
			</tr>
		{%- endfor -%}
		</table>
	</div>
</div>
{#		<div class="clearfix">
			<label for="sub">Sub of</label>
			{{ form.render("sub")}}
		</div>
#}
<div class="container">
	<ul class="pager">
		{%- if offset > 0 -%}
			<li class="previous">{{ link_to(paginatorUri ~ '/' ~ (offset - limitPost), 'Prev', 'rel': 'prev') }}</li>
		{%- endif -%}

		{%- if totalPosts.count > limitPost -%}
			<li class="next">{{ link_to(paginatorUri ~ '/' ~ (offset + limitPost), 'Next', 'rel': 'next') }}</li>
		{%- endif -%}
	</ul>
</div>

{%- else -%}
<div class="container">
	<div class="alert alert-info">There are no posts here</div>
</div>
{%- endif -%}
{%-endif-%}
