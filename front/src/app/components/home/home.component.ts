import { Component, OnInit, DoCheck } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { UserService } from '../../services/user.service';
import { BeerService } from '../../services/beer.service';
import { TypeService } from '../../services/type.service';
import { BreweryService } from '../../services/brewery.service';
import { CountryService } from '../../services/country.service';
import { Beer } from '../../models/beer';
import { Type } from '../../models/type';
import { Brewery } from '../../models/brewery';
import { Country } from '../../models/country';

import * as bootstrap from 'bootstrap';
import * as $AB from 'jquery';

@Component({
    selector: 'app-home',
    templateUrl: './home.component.html',
    styleUrls: ['./home.component.css'],
    providers: [UserService, BeerService, TypeService, BreweryService, CountryService]
})
export class HomeComponent implements OnInit {
    public page_title: string;
    public identity;
    public token;
    public beers;
    public types;
    public breweries;
    public countries;
    public imgFolder;
    public listStyle;
    public loading: boolean;
    public navOpen;
    public filters = {'type': null, 'brewery': null, 'country': null};
    public filteredList;
    public filterBy;
    public detailBeer;
    public page;
    public next_page;
    public prev_page;
    public num_pags;

    constructor(
        private _route: ActivatedRoute,
        private _router: Router,
        private _userService: UserService,
        private _beerService: BeerService,
        private _typeService: TypeService,
        private _breweryService: BreweryService,
        private _countryService: CountryService,
    ) {
  		this.page_title = "Home";
        this.imgFolder = '/assets/img/beers/';
        this.loading = true;
        this.listStyle = false;
        this.navOpen = false;
        this.detailBeer = new Beer(0, '', '', '', '', '', '', '', {'brewery1':'', 'brewery2':''});
        this.filteredList = false;
        this.filterBy = '';
  	}

  	ngOnInit() {
        this._route.params.subscribe(params => {
            this.page = +params['page'];

            this.prev_page = this.page - 1;
            this.next_page = this.page + 1;

            if (!this.page || this.page == 1) {
                this.page = 1;
                this.prev_page = 1;
                this.next_page = 2;
            }
            this.getBeers(this.page);
        });
  		this.loadUser();
        this.getTypes();
        this.getBreweries();
        this.getCountries();
  	}

    ngDoCheck() {
        $('#detailModal').on('hide.bs.modal', e => {
            this.detailBeer = new Beer(0, '', '', '', '', '', '', '', {'brewery1':'', 'brewery2':''});
        });
    }

	loadUser() {
		  this.identity = this._userService.getIdentity();
		  this.token = this._userService.getToken();
	}

    getBeers(page) {
        this.loading = true;
        this._beerService.getBeers(page).subscribe(
            response => {
                if (response.status == 'success') {
                    let beers = response.beers;
                    var num_pags = [];
                    for(var i=1; i<=response.total_pages; i++) {
                        num_pags.push(i);
                    }

                    this.num_pags = num_pags;
                    this.beers = beers;
                    this.loading = false;
                }
            },
            error => {
                console.log(error);
            });
    }

    getTypes() {
        this._typeService.getTypes().subscribe(
            response => {
                if (response.status == 'success') {
                      this.types = this.sortAlphabetically(response.types);
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

    toggleNav() {
        if (this.navOpen == false) {
            this.navOpen = true;
            $("#sideNav").css('width', "250px");
            $('#openClose').css('margin-left', '250px');
            $('#sideNavContainer').css('background-color', 'rgba(0, 0, 0, .55)');
            $('#sideNavContainer').css('width', '100%');
            $('#chevron').css('transform', 'scaleX(-1)');
        } else {
            this.navOpen = false;
            $("#sideNav").css('width', "0");
            $('#openClose').css('margin-left', '0');
            $('#sideNavContainer').css('background-color', 'rgba(0, 0, 0, 0)');
            $('#sideNavContainer').css('width', '0');
            $('#chevron').css('transform', 'scaleX(1)');

            this.filters = {'type': null, 'brewery': null, 'country': null};
        }
    }

    onSubmit(form) {
        if (this.filters.type != null) {
            this.filterBy = {'filter': 'type', 'value': this.filters.type};
        } else if (this.filters.brewery != null) {
            this.filterBy = {'filter': 'brewery', 'value': this.filters.brewery};
        } else if (this.filters.country != null) {
            this.filterBy = {'filter': 'country', 'value': this.filters.country};
        }

        let filterBy = this.filters;
        this.toggleNav();
        this.loading = true;
        this._beerService.getFilteredBeers(filterBy).subscribe(
            response => {
                if (response.status == 'success') {
                    var num_pags = [];
                    for(var i=1; i<=response.total_pages; i++) {
                        num_pags.push(i);
                    }

                    this.num_pags = num_pags;
                    this.beers = response.beers;
                    this.loading = false;
                    this.filteredList = true;
                }
            },
            error => {
                console.log(error);
            }
        );
    }

    sortAlphabetically(array) {
        return array.sort(function(a, b) {
            var textA = a.name.toUpperCase();
            var textB = b.name.toUpperCase();
            return (textA < textB) ? -1 : (textA > textB) ? 1 : 0;
        });
    }

    resetFilters(filter) {
        if (filter == 'type') {
            this.filters.brewery = null;
            this.filters.country = null;
        } else if (filter == 'brewery') {
            this.filters.type = null;
            this.filters.country = null;
        } else if (filter == 'country') {
            this.filters.type = null;
            this.filters.brewery = null;
        }
    }

    setDetailBeer(beer) {
        this.detailBeer = beer;
    }

    log() {
        console.log(this.filters);
    }
}