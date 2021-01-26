<div class="row">
    <div class="col-md-6">
        <div class="page-header">
            <h2>用户登录</h2>
        </div>

        <form action="/user/login" role="form" method="post">
            <fieldset>
                <div class="form-group">
                    <label for="email">Username/Email</label>
                    <div class="controls">
                        {{ text_field('email', 'class': "form-control") }}
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="controls">
                        {{ password_field('password', 'class': "form-control") }}
                    </div>
                </div>
                <div class="form-group">
                    {{ submit_button('登录', 'class': 'btn btn-primary btn-large') }}
                    {{ link_to('user/register', '注册', 'class': 'btn btn-primary btn-large btn-success') }}
                </div>
            </fieldset>
        </form>
    </div>

</div>
