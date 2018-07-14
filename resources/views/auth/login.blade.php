@include("layout.partials.header")

<div class="container navbar-container">

	@include('auth.notice')

	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-default">
				<div class="panel-heading">{{ trans('common.login') }}</div>
				<div class="panel-body">
					<form class="form-horizontal" role="form" method="POST" action="/auth/login" autocomplete="off">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">

						<div class="form-group">
							<label class="col-md-4 control-label">{{ trans('common.fields.email_address') }}</label>
							<div class="col-md-6">
                                <div class="input-group margin-bottom-sm">
                                    <span class="input-group-addon"><i class="fa fa-envelope-o fa-fw"></i></span>
                                    <input class="form-control" type="text" name="email" placeholder="email@address.com" value="{{ old('email') }}">
                                </div>
							</div>
						</div>

						<div class="form-group">
							<label class="col-md-4 control-label">{{ trans('common.fields.password') }}</label>
							<div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-lock fa-fw"></i></span>
                                    <input class="form-control" type="password" name="password" placeholder="Password">
                                </div>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<div class="checkbox">
									<label>
										<input type="checkbox" name="remember"> {{ trans('common.fields.remember_me') }}
									</label>
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-6 col-md-offset-4">
								<button type="submit" class="btn tweekly-brand-button" style="margin-right: 15px;">
									{{ trans('common.login') }}
								</button>

								<a href="/password/email">{{ trans('common.fields.forgot_password') }}</a>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

@include("layout.partials.footer")
