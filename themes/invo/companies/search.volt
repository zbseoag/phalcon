<ul class="pager">
    <li class="previous pull-left">
        {{ link_to("companies/index", "&larr; Back") }}
        {{ link_to("companies/new", "Create") }}
    </li>
</ul>


<table class="table table-bordered table-striped">
<thead>
<tr>
    <th>Id</th>
    <th>Name</th>
    <th>Telephone</th>
    <th>Address</th>
    <th>City</th>
    <th colspan="2">操作</th>
</tr>
</thead>
<tbody>
{% for item in page.items %}
    <tr>
        <td>{{ item.id }}</td>
        <td>{{ item.name }}</td>
        <td>{{ item.telephone }}</td>
        <td>{{ item.address }}</td>
        <td>{{ item.city }}</td>
        <td width="7%">{{ link_to("companies/edit/" ~ item.id, '<i class="glyphicon glyphicon-edit"></i> Edit', "class": "btn btn-default") }}</td>
        <td width="7%">{{ link_to("companies/delete/" ~ item.id, '<i class="glyphicon glyphicon-remove"></i> Delete', "class": "btn btn-default") }}</td>
    </tr>
{% else %}
    <tr><td colspan="7">没有找到记录</td></tr>
{% endfor %}
</tbody>
</table>

<div class="btn-group">
    {{ link_to("companies/search", '<i class="icon-fast-backward"></i> First', "class": "btn btn-default") }}
    {{ link_to("companies/search?page=" ~ page.previous, '<i class="icon-step-backward"></i> Previous', "class": "btn btn-default") }}
    {{ link_to("companies/search?page=" ~ page.next, '<i class="icon-step-forward"></i> Next', "class": "btn btn-default") }}
    {{ link_to("companies/search?page=" ~ page.last, '<i class="icon-fast-forward"></i> Last', "class": "btn btn-default") }}
    <span>{{ page.current }}/{{ page.last }}</span>
</div>








{% for item in page.items %}

    {% if loop.first %}
        <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th colspan="2">操作</th>
        </tr>
        </thead>
        <tbody>
    {% endif %}
        <!-- 数据-->
    {% if loop.last %}
    </tbody>
    </table>
    {% endif %}
{% else %}
<!-- 没有数据-->
{% endfor %}







