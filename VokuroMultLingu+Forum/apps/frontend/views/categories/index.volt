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

		{%- for categorie in categories -%}
{%if !categorie.sub%} {# Ajout tres important pour n'ajouter simplement que les categories + securite a reflechir pour qu aumoins une ne disparaisse jamais#}
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
            <td>{%if !gotsub%}{{ link_to('category/' ~ categorie.id ~ '/' ~ categorie.slug, categorie.name) }}
			<br><small>{{ categorie.description }}</small>{%endif%}</td>
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
{%endif%}
		{%- endfor -%}

        </tbody>
      </table>

		</div>
	</div>
</div>
