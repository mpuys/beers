import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { User } from '../../models/user';
import { UserService } from '../../services/user.service';

@Component({
  selector: 'login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css'],
  providers: [UserService]
})
export class LoginComponent implements OnInit {

	public page_title: string;
	public user: User;
	public identity;
	public token: string;

  	constructor(
  		private _userService: UserService,
  		private _router: Router,
  		private _route: ActivatedRoute
  	) {
  		this.page_title = "Login";
  		this.user = new User(0, '', '');
  	}

	ngOnInit() {
		this.logout();
	}

	onSubmit(form) {
		this._userService.login(this.user).subscribe(
			response => {
				if (!response.status || response.status != 'error') {
					// this.status = 'success';
					this.identity = response;

					this._userService.login(this.user, true).subscribe(
						response => {
							if (!response.status || response.status != 'error') {
								// this.status = 'success';
								this.token = response;

								localStorage.setItem('token', this.token);
								localStorage.setItem('identity', JSON.stringify(this.identity));

								this._router.navigate(['/home']);
							}
						},
						error => {
							console.log(error);
							// this.status = 'error';
						}
					);
				}
			},
			error => {
				console.log(error);
				// this.status = 'error';
			}
		);
	}

	logout() {
		this._route.params.subscribe(params => {
			let sure = +params['sure'];

			if (sure == 1) {
				localStorage.removeItem('identity');
				localStorage.removeItem('token');

				this.identity = null;
				this.token = null;

				this._router.navigate(['/home']);
			}
		});
	}
}
