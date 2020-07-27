import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { BeerNewComponent } from './beer-new.component';

describe('BeerNewComponent', () => {
  let component: BeerNewComponent;
  let fixture: ComponentFixture<BeerNewComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ BeerNewComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(BeerNewComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
