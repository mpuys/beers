import { ModuleWithProviders } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { LoginComponent } from './components/login/login.component';
import { HomeComponent } from './components/home/home.component';
import { BeerNewComponent } from './components/beer-new/beer-new.component';
import { BeerEditComponent } from './components/beer-edit/beer-edit.component';
import { ErrorComponent } from './components/error/error.component';

import { IdentityGuard } from './services/identity.guard';

const appRoutes: Routes = [
	{path: '', component: HomeComponent},
	{path: 'home', component: HomeComponent},
	{path: 'home/:page', component: HomeComponent},
	{path: 'login', component: LoginComponent},
	{path: 'beer/new', component: BeerNewComponent, canActivate: [IdentityGuard]},
	{path: 'beer/edit/:id', component: BeerEditComponent, canActivate: [IdentityGuard]},
	{path: 'logout/:sure', component: LoginComponent},
	{path: 'error', component: ErrorComponent},
	{path: '**', component: ErrorComponent},
];

export const appRoutingProviders: any[] = [];
export const routing: ModuleWithProviders = RouterModule.forRoot(appRoutes);