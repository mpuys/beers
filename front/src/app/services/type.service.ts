import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Type } from '../models/type';
import { global } from './global';

@Injectable()
export class TypeService {

	public url: string;
	public headers;

	constructor(
		public _http:HttpClient
	) {
		this.url = global.url;
		this.headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
	}

	getTypes():Observable<any>  {
		return this._http.get(this.url+'type/list', {headers: this.headers});
	}
}