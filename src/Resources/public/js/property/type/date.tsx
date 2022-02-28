import * as React from 'react';
import { Property, PropertyFactory } from './property';
import PropertyDto from './property-dto';

class Date implements Property {
    render(propertyDto: PropertyDto): React.ReactNode {
        return (
            <React.Fragment key={propertyDto.code + propertyDto.locale}>
                <div className="AknFieldContainer-inputContainer field-input">
                    <input
                        id={propertyDto.createId()}
                        type={'date'}
                        value={propertyDto.value}
                        className={'AknTextField'}
                        onChange={(event: React.ChangeEvent<HTMLInputElement>): void => {
                            propertyDto.updateValue(event.target.value);
                        }}
                    />
                </div>
            </React.Fragment>
        );
    }
}

const factory: PropertyFactory = (): Property => new Date();

// ts-unused-exports:disable-next-line
export default factory;
