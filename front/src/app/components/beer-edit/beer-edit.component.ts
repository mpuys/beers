import { Component, OnInit, DoCheck } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { UserService } from '../../services/user.service';
import { BeerService } from '../../services/beer.service';
import { TypeService } from '../../services/type.service';
import { BreweryService } from '../../services/brewery.service';
import { CountryService } from '../../services/country.service';
import { Beer } from '../../models/beer';
import { Brewery } from '../../models/brewery';
import { Country } from '../../models/country';

import * as bootstrap from 'bootstrap';
import * as $AB from 'jquery';

@Component({
	selector: 'app-beer-edit',
	templateUrl: './beer-edit.component.html',
	styleUrls: ['./beer-edit.component.css'],
	providers: [UserService, BeerService, TypeService, BreweryService, CountryService]
})
export class BeerEditComponent implements OnInit, DoCheck {
	public page_title: string;
	public identity;
	public token;
	public beer: Beer;
	public newBrewery: Brewery;
	public newCountry: Country;
	public types;
	public breweries;
	public countries;
	public beer_img: File;
	public newBrew1_logo: File;
	public newBrew2_logo: File;
	public collab;
	public loading: boolean;

	constructor(
	  	private _route: ActivatedRoute,
	  	private _router: Router,
	  	private _userService: UserService,
	  	private _beerService: BeerService,
	  	private _typeService: TypeService,
	  	private _breweryService: BreweryService,
	  	private _countryService: CountryService
	) {
		this.page_title = "Edit Beer";
		this.identity = this._userService.getIdentity();
		this.token = this._userService.getToken();
		this.loading = true;
	}

	ngOnInit() {
		this.collab = false;
		this.beer = new Beer(0, '', '', '', '', '', '', '', {'brewery1': '', 'brewery2': ''});
		this.newBrewery = new Brewery(0, '', '', '', '', '');
		this.newCountry = new Country(0, '');
		this.getBeer();
		this.getTypes();
		this.getBreweries();
		this.getCountries();
	}

	ngDoCheck() {
		if (typeof this.beer !== 'undefined') {
			if (this.beer.type == 'other') {
				this.beer.type = '';
				$('#new-type-modal').modal("show");
			}

			if (typeof this.beer.brewery !== 'undefined') {
				if (typeof this.beer.brewery.brewery1 !== 'undefined' && this.beer.brewery.brewery1 == 'other') {
					this.beer.brewery.brewery1 = this.newBrewery;
					$('#new-brewery-modal').modal("show");
				}

				if (typeof this.beer.brewery.brewery2 !== 'undefined' && this.beer.brewery.brewery2 == 'other') {
					this.beer.brewery.brewery2 = this.newBrewery;
					$('#new-brewery-modal').modal("show");
				}
			}

			if (this.newBrewery.country == 'other') {
				this.newBrewery.country = null;
			}
		}
	}

	getBeer() {
  		this._route.params.subscribe(params => {
  			var id = +params['id'];

	  		this._beerService.getBeer(id).subscribe(
	  			response => {
	  				if (response.status == 'success') {
	  					if (typeof response.beer.breweries[1] == 'undefined') {
	  						response.beer.breweries[1] = {'id':0};
	  					}

	  					response.beer.brewery = response.beer.breweries;
	  					delete response.beer.breweries;
	  					this.beer = response.beer;

	  					let breweries = this.beer.brewery;
	  					this.beer.brewery = {'brewery1': breweries[0], 'brewery2':breweries[1]};
	  					this.loading = false;
	  				} else {
	  					this._router.navigate(['/home']);
	  				}
	  			},
	  			error => {
	  				console.log(error);
	  			}
	  		);
  		});
	}

	getTypes() {
		this._typeService.getTypes().subscribe(
			response => {
				if (response.status == 'success') {
		  			this.types = this.sortAlphabetically(response.types);
		  			// console.log(typeof this.types[0].id);
				}
			},
			error => {
				this._typeService.getTypes().subscribe(
		  		response => {
		  			if (response.status == 'success') {
		  				this.types = this.sortAlphabetically(response.types);
		  			}
		  		},
		  		error => {
		  			this._router.navigate(['/home']);
		  		});
			}
		);
	}

	getBreweries() {
		this._breweryService.getBreweries().subscribe(
			response => {
				if (response.status == 'success') {
					this.breweries = this.sortAlphabetically(response.all_breweries);
				}
			},
			error => {
				this._breweryService.getBreweries().subscribe(
		  		response => {
		  			if (response.status == 'success') {
		  				this.breweries = this.sortAlphabetically(response.all_breweries);
		  			}
		  		},
		  		error => {
		  			this._router.navigate(['/home']);
		  		});
			}
		);
	}

	getCountries() {
		this._countryService.getCountries().subscribe(
			response => {
				if (response.status == 'success') {
		  			this.countries = this.sortAlphabetically(response.countries);
				}
			},
			error => {
				this._countryService.getCountries().subscribe(
		  		response => {
		  			if (response.status == 'success') {
		  				this.countries = this.sortAlphabetically(response.countries);
		  			}
		  		},
		  		error => {
		  			this._router.navigate(['/home']);
		  		});
			}
		);
	}

	onFileSelected(file, item) {
		if (item == 'beer') {
			this.beer_img = file;
		}

		if (item == 'brewery') {
			let filename = file.name.substring(0, file.name.lastIndexOf("."))
			if (this.beer.brewery.brewery1.name.toLowerCase() == filename.toLowerCase().replace('_', ' ')) {
				this.newBrew1_logo = file;
			}

			if (this.beer.brewery.brewery2.name.toLowerCase() == filename.toLowerCase().replace('_', ' ')) {
				this.newBrew2_logo = file;
			}
		}
	}

	onSubmit(form) {
		this.beer.brewery.brewery2.name = this.beer.brewery.brewery2.name ? this.beer.brewery.brewery2.name : '';
		this._beerService.newBeer(this.beer, this.beer_img, this.newBrew1_logo, this.newBrew2_logo).subscribe(
			response => {
				console.log(response);
				if (response.status == 'success') {
					this._router.navigate(['/home']);
				}
			},
			error => {
				console.log(error);
			}
		);
	}

	delete() {
		this._beerService.delete(this.beer).subscribe(
			response => {
				console.log(response);
				if (response.status == 'success') {
					this._router.navigate(['/home']);
				}
			},
			error => {
				console.log(error);
			}
		);
	}

	onSaveNewType() {
		var type = this.beer.type;
		this.beer.type = {'id':0,'name':type};
		this.types.push(this.beer.type);
		this.types = this.sortAlphabetically(this.types);
	}

	onSaveNewBrewery() {
		if (this.newCountry.name != '') {
			this.newBrewery.country = this.newCountry;
			this.onSaveNewCountry();
		}

		this.beer.brewery.brewery1 = this.newBrewery;
		this.breweries.push(this.newBrewery);
		this.breweries = this.sortAlphabetically(this.breweries);
		this.newBrewery = new Brewery(0, '', '', '', '', '');
	}

	onSaveNewCountry() {
		this.countries.push(this.newCountry);
		this.countries = this.sortAlphabetically(this.countries);
		this.newCountry = new Country(0, '');
	}

	sortAlphabetically(array) {
		return array.sort(function(a, b) {
		    var textA = a.name.toUpperCase();
		    var textB = b.name.toUpperCase();
		    return (textA < textB) ? -1 : (textA > textB) ? 1 : 0;
		});
	}
}
