import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Brewery } from '../models/brewery';
import { global } from './global';

@Injectable()
export class BreweryService {

	public url: string;
	public types;
	public headers;

	constructor(
		public _http:HttpClient
	) {
		this.url = global.url;
		this.headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
	}

	getBreweries():Observable<any>  {
		return this._http.get(this.url+'brewery/list', {headers: this.headers});
	}
}