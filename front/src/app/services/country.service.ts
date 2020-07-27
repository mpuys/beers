import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Country } from '../models/country';
import { global } from './global';

@Injectable()
export class CountryService {

	public url: string;
	public headers;

	constructor(
		public _http:HttpClient
	) {
		this.url = global.url;
		this.headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
	}

	getCountries():Observable<any>  {
		return this._http.get(this.url+'country/list', {headers: this.headers});
	}
}