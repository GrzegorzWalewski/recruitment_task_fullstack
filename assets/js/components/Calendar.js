import React from 'react';

export default function SimpleDatePicker({setSelectedDate, selectedDate}) {
  const handleDateChange = (e) => {
    setSelectedDate(e.target.value);
  };

  const today = new Date().toISOString().split('T')[0];

  return (
      <div className='text-center'>
          <label htmlFor="date">Choose a date: </label>
          <input
                type="date"
                value={selectedDate}
                onChange={handleDateChange}
                min="2023-01-01"
                max={today}
            />
      </div>
  );
};