import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { User } from '../models/user';
import { global } from './global';

@Injectable()
export class UserService {

	public url: string;
	public identity;
	public token: string;

	constructor(
		public _http:HttpClient
	) {
		this.url = global.url;
	}

	login(user, get_token = null):Observable<any> {
		if (get_token != null) {
			user.get_token = 'true';
		}

		let json = JSON.stringify(user);
		let params = 'json='+json;

		let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');

		return this._http.post(this.url+'login', params, {headers: headers});
	}

	getIdentity(){
		let identity = JSON.parse(localStorage.getItem('identity'));

		this.identity = null;
		if (identity && identity != 'undefined') {
			this.identity = identity;
		}

		return this.identity;
	}

	getToken(){
		let token = localStorage.getItem('token');

		this.token = null;
		if (token && token != 'undefined') {
			this.token = token;
		}

		return this.token;
	}
}