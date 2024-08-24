import React from 'react';

export default function SimpleDatePicker({setSelectedDate, selectedDate}) {
  console.log(setSelectedDate);
  console.log('arst');
  const handleDateChange = (e) => {
    setSelectedDate(e.target.value);
  };

  return (
      <div className='text-center'>
          <label htmlFor="date">Choose a date: </label>
          <input
                type="date"
                value={selectedDate}
                onChange={handleDateChange}
                min="2023-01-01"
            />
      </div>
  );
};