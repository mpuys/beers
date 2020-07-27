import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Beer } from '../models/beer';
import { global } from './global';

import { UserService } from '../services/user.service';

@Injectable()
export class BeerService {

	public url: string;
	public types;
	public headers;
	public token;

	constructor(
		public _http:HttpClient,
		public _userService: UserService
	) {
		this.url = global.url;
		this.headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
		this.token = this._userService.getToken();
	}

	getBeers(page):Observable<any>  {
		if (!page) {
			page = 1;
		}

		return this._http.get(this.url+'beer/list?page='+page, {headers: this.headers});
	}

	getFilteredBeers(filters): Observable<any> {
		let json = JSON.stringify(filters);
		let params = 'json='+json;

		return this._http.post(this.url+'beer/list', params, {headers: this.headers})
	}

	getBeer(id):Observable<any>  {
		return this._http.get(this.url+'beer/detail/'+id, {headers: this.headers});
	}

	newBeer(beer, beer_img, newBrew1_logo, newBrew2_logo):Observable<any> {
		var formData = new FormData();
		formData.append('beer', JSON.stringify(beer));
		formData.append('img', beer_img);
		(newBrew1_logo) && formData.append('newBrew1_logo', newBrew1_logo);
		(newBrew2_logo) && formData.append('newBrew2_logo', newBrew2_logo);

		let headers = new HttpHeaders().set('Authorization', this.token);

		return this._http.post(this.url+'beer/new', formData, {headers: headers});
	}

	delete(beer):Observable<any> {
		let headers = new HttpHeaders().set('Authorization', this.token);
		
		return this._http.delete(this.url+'beer/delete/'+beer.id, {headers: headers});
	}
}