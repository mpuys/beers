import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { HttpClientModule } from '@angular/common/http';
import { routing, appRoutingProviders } from './app.routing';
import { FormsModule, FormGroup, FormControl, ReactiveFormsModule } from '@angular/forms';
// import { NgSelect2Module } from 'select2';

import { AppComponent } from './app.component';
import { HomeComponent } from './components/home/home.component';
import { LoginComponent } from './components/login/login.component';
import { ErrorComponent } from './components/error/error.component';
import { BeerNewComponent } from './components/beer-new/beer-new.component';
import { BeerEditComponent } from './components/beer-edit/beer-edit.component';

import { IdentityGuard } from './services/identity.guard';
import { UserService } from './services/user.service';
import { LoadingComponent } from './components/loading/loading.component';

@NgModule({
  declarations: [
    AppComponent,
    HomeComponent,
    LoginComponent,
    ErrorComponent,
    BeerNewComponent,
    BeerEditComponent,
    LoadingComponent
  ],
  imports: [
    BrowserModule,
    routing,
    FormsModule,
    ReactiveFormsModule,
    HttpClientModule,
    // NgSelect2Module
  ],
  providers: [
    appRoutingProviders,
    IdentityGuard,
    UserService
  ],
  bootstrap: [AppComponent]
})
export class AppModule { }
